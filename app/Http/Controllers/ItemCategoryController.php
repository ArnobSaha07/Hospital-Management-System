<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemCategoryRequest;
use App\Http\Requests\UpdateItemCategoryRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Repositories\ItemCategoryRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemCategoryController extends AppBaseController
{
    /** @var ItemCategoryRepository */
    private $itemCategoryRepository;

    public function __construct(ItemCategoryRepository $itemCategoryRepo)
    {
        $this->middleware('check_menu_access');
        $this->itemCategoryRepository = $itemCategoryRepo;
    }

    /**
     * Display a listing of the ItemCategory.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('item_categories.index');
    }

    /**
     * Store a newly created ItemCategory in storage.
     */
    public function store(CreateItemCategoryRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->itemCategoryRepository->create($input);

        return $this->sendSuccess(__('messages.flash.item_category_saved'));
    }

    /**
     * Show the form for editing the specified ItemCategory.
     */
    public function edit(ItemCategory $itemCategory): JsonResponse
    {
        if (! canAccessRecord(ItemCategory::class, $itemCategory->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($itemCategory, __('messages.flash.item_category_retrieved'));
    }

    /**
     * Update the specified ItemCategory in storage.
     */
    public function update(ItemCategory $itemCategory, UpdateItemCategoryRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->itemCategoryRepository->update($input, $itemCategory->id);

        return $this->sendSuccess(__('messages.flash.item_category_updated'));
    }

    /**
     * Remove the specified ItemCategory from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(ItemCategory $itemCategory): JsonResponse
    {
        if (! canAccessRecord(ItemCategory::class, $itemCategory->id)) {
            return $this->sendError(__('messages.flash.item_category_not_found'));
        }

        $itemCategoryModel = [Item::class];
        $result = canDelete($itemCategoryModel, 'item_category_id', $itemCategory->id);
        if ($result) {
            return $this->sendError(__('messages.flash.item_category_cant_deleted'));
        }
        $this->itemCategoryRepository->delete($itemCategory->id);

        return $this->sendSuccess(__('messages.flash.item_category_deleted'));
    }

    public function getItemsList(Request $request): JsonResponse
    {
        if (empty($request->get('id'))) {
            return $this->sendError(__('messages.flash.item_not_found'));
        }

        $itemsData = Item::get()->where('item_category_id', $request->get('id'))->pluck('name', 'id');

        return $this->sendResponse($itemsData, __('messages.flash.retrieve'));
    }
}
