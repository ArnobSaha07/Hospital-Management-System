<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOperationReportRequest;
use App\Http\Requests\UpdateOperationReportRequest;
use App\Models\OperationReport;
use App\Models\PatientCase;
use App\Repositories\OperationReportRepository;
use Carbon\Carbon;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OperationReportController extends AppBaseController
{
    /** @var OperationReportRepository */
    private $operationReportRepository;

    public function __construct(OperationReportRepository $operationReportRepo)
    {
        $this->middleware('check_menu_access');
        $this->operationReportRepository = $operationReportRepo;
    }

    /**
     * Display a listing of the OperationReport.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $doctors = $this->operationReportRepository->getDoctors();
        $cases = $this->operationReportRepository->getCases();

        return view('operation_reports.index', compact('doctors', 'cases'));
    }

    /**
     * Store a newly created OperationReport in storage.
     */
    public function store(CreateOperationReportRequest $request): JsonResponse
    {
        $input = $request->all();
        $patientId = PatientCase::with('patient.patientUser')->whereCaseId($input['case_id'])->first();
        $birthDate = $patientId->patient->patientUser->dob;
        $operationDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $operationDate < $birthDate) {
            return $this->sendError(__('messages.flash.date_smaller'));
        }
        $this->operationReportRepository->store($input);

        return $this->sendSuccess(__('messages.flash.operation_report_saved'));
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show(OperationReport $operationReport)
    {
        if (! canAccessRecord(OperationReport::class, $operationReport->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $doctors = $this->operationReportRepository->getDoctors();
        $cases = $this->operationReportRepository->getCases();

        return view('operation_reports.show')->with([
            'operationReport' => $operationReport, 'doctors' => $doctors, 'cases' => $cases,
        ]);
    }

    /**
     * Show the form for editing the specified OperationReport.
     */
    public function edit(OperationReport $operationReport): JsonResponse
    {
        if (! canAccessRecord(OperationReport::class, $operationReport->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = OperationReport::whereId($operationReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return $this->sendError(__('messages.flash.operation_report_not_found'));
            }
        }

        return $this->sendResponse($operationReport, __('messages.flash.operation_report_retrieved'));
    }

    /**
     * Update the specified OperationReport in storage.
     */
    public function update(OperationReport $operationReport, UpdateOperationReportRequest $request): JsonResponse
    {
        $input = $request->all();
        $patientId = PatientCase::with('patient.patientUser')->whereCaseId($input['case_id'])->first();
        $birthDate = $patientId->patient->patientUser->dob;
        $operationDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $operationDate < $birthDate) {
            return $this->sendError(__('messages.flash.date_smaller'));
        }
        $this->operationReportRepository->update($input, $operationReport);

        return $this->sendSuccess(__('messages.flash.operation_report_updated'));
    }

    /**
     * Remove the specified OperationReport from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(OperationReport $operationReport): JsonResponse
    {
        if (! canAccessRecord(OperationReport::class, $operationReport->id)) {
            return $this->sendError(__('messages.flash.operation_report_not_found'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = OperationReport::whereId($operationReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return $this->sendError(__('messages.flash.operation_report_not_found'));
            }
        }

        $operationReport->delete();

        return $this->sendSuccess(__('messages.flash.operation_report_deleted'));
    }
}
