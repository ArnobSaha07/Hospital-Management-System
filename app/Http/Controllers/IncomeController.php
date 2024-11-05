<?php

namespace App\Http\Controllers;

use App\Exports\IncomeExport;
use App\Http\Requests\CreateIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\Income;
use App\Repositories\IncomeRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IncomeController extends AppBaseController
{
    /**
     * @var IncomeRepository
     */
    private $incomeRepository;

    public function __construct(IncomeRepository $incomeRepository)
    {
        $this->incomeRepository = $incomeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $incomeHeads = Income::INCOME_HEAD;
        asort($incomeHeads);
        $filterIncomeHeads = Income::FILTER_INCOME_HEAD;
        asort($filterIncomeHeads);

        return view('incomes.index', compact('incomeHeads', 'filterIncomeHeads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateIncomeRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);
        $this->incomeRepository->store($input);
        $this->incomeRepository->createNotification($input);

        return $this->sendSuccess(__('messages.flash.income_saved'));
    }

    /**
     * @return Application|Factory|View
     */
    public function show(Income $income)
    {
        if (! canAccessRecord(Income::class, $income->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $incomes = $this->incomeRepository->find($income->id);
        $incomeHeads = Income::INCOME_HEAD;
        asort($incomeHeads);

        return view('incomes.show', compact('incomes', 'incomeHeads'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income): JsonResponse
    {
        if (! canAccessRecord(Income::class, $income->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($income, __('messages.flash.income_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncomeRequest $request, Income $income): JsonResponse
    {
        $this->incomeRepository->updateExpense($request->all(), $income->id);

        return $this->sendSuccess(__('messages.flash.income_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Income $income): JsonResponse
    {
        if (! canAccessRecord(Income::class, $income->id)) {
            return $this->sendError(__('messages.flash.income_not_found'));
        }

        $this->incomeRepository->deleteDocument($income->id);

        return $this->sendSuccess(__('messages.flash.income_deleted'));
    }

    /**
     * @return ResponseFactory|\Illuminate\Http\Response
     *
     * @throws FileNotFoundException
     */
    public function downloadMedia(Income $income)
    {
        if (! canAccessRecord(Income::class, $income->id)) {
            return Redirect::back();
        }

        $documentMedia = $income->media[0];
        $documentPath = $documentMedia->getPath();
        if (config('app.media_disc') === 'public') {
            $documentPath = (Str::after($documentMedia->getUrl(), '/uploads'));
        }

        ob_end_clean();

        $file = Storage::disk(config('app.media_disc'))->get($documentPath);

        $headers = [
            'Content-Type' => $income->media[0]->mime_type,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$income->media[0]->file_name}",
            'filename' => $income->media[0]->file_name,
        ];

        return response($file, 200, $headers);
    }

    public function incomeExport(): BinaryFileResponse
    {
        $response = Excel::download(new IncomeExport, 'incomes-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
