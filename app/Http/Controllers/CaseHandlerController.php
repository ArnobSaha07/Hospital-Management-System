<?php

namespace App\Http\Controllers;

use App\Exports\CaseHandlerExport;
use App\Http\Requests\CreateCaseHandlerRequest;
use App\Http\Requests\UpdateCaseHandlerRequest;
use App\Models\CaseHandler;
use App\Models\EmployeePayroll;
use App\Repositories\CaseHandlerRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CaseHandlerController extends AppBaseController
{
    /** @var CaseHandlerRepository */
    private $caseHandlerRepository;

    public function __construct(CaseHandlerRepository $caseHandlerRepo)
    {
        $this->caseHandlerRepository = $caseHandlerRepo;
    }

    /**
     * Display a listing of the CaseHandler.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = CaseHandler::STATUS_ARR;

        return view('case_handlers.index', $data);
    }

    /**
     * Show the form for creating a new CaseHandler.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $bloodGroup = getBloodGroups();

        return view('case_handlers.create', compact('bloodGroup'));
    }

    /**
     * Store a newly created CaseHandler in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateCaseHandlerRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = ! isset($input['status']) ? 0 : 1;
        $input['region_code'] = regionCode($input['prefix_code']);

        $this->caseHandlerRepository->store($input);
        Flash::success(__('messages.flash.case_handler_saved'));

        return redirect(route('case-handlers.index'));
    }

    /**
     * Display the specified CaseHandler.
     *
     * @return Factory|View
     */
    public function show(CaseHandler $caseHandler): View
    {
        $payrolls = $caseHandler->payrolls;

        return view('case_handlers.show', compact('caseHandler', 'payrolls'));
    }

    /**
     * Show the form for editing the specified CaseHandler.
     *
     * @return Factory|View
     */
    public function edit(CaseHandler $caseHandler)
    {
        if (! canAccessRecord(CaseHandler::class, $caseHandler->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $user = $caseHandler->user;
        $bloodGroup = getBloodGroups();

        return view('case_handlers.edit', compact('user', 'caseHandler', 'bloodGroup'));
    }

    /**
     * Update the specified CaseHandler in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(CaseHandler $caseHandler, UpdateCaseHandlerRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['region_code'] = regionCode($input['prefix_code']);
        
        $this->caseHandlerRepository->update($caseHandler, $input);
        Flash::success(__('messages.flash.case_handler_updated'));

        return redirect(route('case-handlers.index'));
    }

    /**
     * Remove the specified CaseHandler from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(CaseHandler $caseHandler): JsonResponse
    {
        if (! canAccessRecord(CaseHandler::class, $caseHandler->id)) {
            return $this->sendError(__('messages.flash.case_handler_not_found'));
        }

        $caseHandlersModels = [
            EmployeePayroll::class,
        ];
        $result = canDelete($caseHandlersModels, 'owner_id', $caseHandler->id);
        if ($result) {
            return $this->sendError(__('messages.flash.case_handler_cant_deleted'));
        }

        $caseHandler->user()->delete();
        $caseHandler->address()->delete();
        $caseHandler->delete();

        return $this->sendSuccess(__('messages.flash.case_handler_deleted'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        $caseHandler = CaseHandler::findOrFail($id);
        $status = ! $caseHandler->user->status;
        $caseHandler->user()->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function caseHandlerExport(): BinaryFileResponse
    {
        $response = Excel::download(new CaseHandlerExport, 'case-handlers-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
