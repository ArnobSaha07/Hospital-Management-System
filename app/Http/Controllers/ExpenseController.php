<?php

namespace App\Http\Controllers;

use App\Exports\ExpenseExport;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
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

class ExpenseController extends AppBaseController
{
    /**
     * @var ExpenseRepository
     */
    private $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
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
        $expenseHeads = Expense::EXPENSE_HEAD;
        asort($expenseHeads);
        $filterExpenseHeads = Expense::FILTER_EXPENSE_HEAD;
        asort($filterExpenseHeads);

        return view('expenses.index', compact('expenseHeads', 'filterExpenseHeads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateExpenseRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);
        $this->expenseRepository->store($input);
        $this->expenseRepository->createNotification($input);

        return $this->sendSuccess(__('messages.flash.expense_saved'));
    }

    /**
     * @return Application|Factory|View
     */
    public function show(Expense $expense)
    {
        if (! canAccessRecord(Expense::class, $expense->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $expenses = $this->expenseRepository->find($expense->id);
        $expenseHeads = Expense::EXPENSE_HEAD;
        asort($expenseHeads);

        return view('expenses.show', compact('expenses', 'expenseHeads'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense): JsonResponse
    {
        if (! canAccessRecord(Expense::class, $expense->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($expense, __('messages.flash.expense_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->expenseRepository->updateExpense($request->all(), $expense->id);

        return $this->sendSuccess(__('messages.flash.expense_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Expense $expense): JsonResponse
    {
        if (! canAccessRecord(Expense::class, $expense->id)) {
            return $this->sendError(__('messages.flash.expense_not_found'));
        }

        $this->expenseRepository->deleteDocument($expense->id);

        return $this->sendSuccess(__('messages.flash.expense_deleted'));
    }

    /**
     * @return ResponseFactory|\Illuminate\Http\Response
     *
     * @throws FileNotFoundException
     */
    public function downloadMedia(Expense $expense)
    {
        if (! canAccessRecord(Expense::class, $expense->id)) {
            return Redirect::back();
        }

        $documentMedia = $expense->media[0];
        $documentPath = $documentMedia->getPath();
        if (config('app.media_disc') === 'public') {
            $documentPath = (Str::after($documentMedia->getUrl(), '/uploads'));
        }

        ob_end_clean();

        $file = Storage::disk(config('app.media_disc'))->get($documentPath);

        $headers = [
            'Content-Type' => $expense->media[0]->mime_type,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$expense->media[0]->file_name}",
            'filename' => $expense->media[0]->file_name,
        ];

        return response($file, 200, $headers);
    }

    public function expenseExport(): BinaryFileResponse
    {
        $response = Excel::download(new ExpenseExport, 'expenses-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
