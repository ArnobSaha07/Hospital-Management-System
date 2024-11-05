<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemStockRequest;
use App\Http\Requests\UpdateItemStockRequest;
use App\Models\ItemStock;
use App\Repositories\ItemStockRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class ItemStockController extends AppBaseController
{
    /** @var ItemStockRepository */
    private $itemStockRepository;

    public function __construct(ItemStockRepository $itemStockRepo)
    {
        $this->middleware('check_menu_access');
        $this->itemStockRepository = $itemStockRepo;
    }

    /**
     * Display a listing of the ItemStock.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('item_stocks.index');
    }

    /**
     * Show the form for creating a new ItemStock.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $itemCategories = $this->itemStockRepository->getItemCategories();
        natcasesort($itemCategories);

        return view('item_stocks.create', compact('itemCategories'));
    }

    /**
     * Store a newly created ItemStock in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function store(CreateItemStockRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['purchase_price'] = removeCommaFromNumbers($input['purchase_price']);
        $this->itemStockRepository->store($input);
        Flash::success(__('messages.flash.item_stock_saved'));

        return redirect(route('item.stock.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(ItemStock $itemStock)
    {
        if (! canAccessRecord(ItemStock::class, $itemStock->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('item_stocks.show', compact('itemStock'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(ItemStock $itemStock)
    {
        if (! canAccessRecord(ItemStock::class, $itemStock->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $itemCategories = $this->itemStockRepository->getItemCategories();
        natcasesort($itemCategories);

        return view('item_stocks.edit', compact('itemCategories', 'itemStock'));
    }

    /**
     * Update the specified ItemStock in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function update(ItemStock $itemStock, UpdateItemStockRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['purchase_price'] = removeCommaFromNumbers($input['purchase_price']);
        $this->itemStockRepository->update($itemStock, $input);
        Flash::success(__('messages.flash.item_stock_updated'));

        return redirect(route('item.stock.index'));
    }

    /**
     * Remove the specified ItemStock from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(ItemStock $itemStock): JsonResponse
    {
        if (! canAccessRecord(ItemStock::class, $itemStock->id)) {
            return $this->sendError(__('messages.flash.item_stock_not_found'));
        }

        $this->itemStockRepository->destroyItemStock($itemStock);

        return $this->sendSuccess(__('messages.flash.item_stock_deleted'));
    }

    public function downloadMedia(ItemStock $itemStock): Response
    {
        [$file, $headers] = $this->itemStockRepository->downloadMedia($itemStock);

        return response($file, 200, $headers);
    }
}
