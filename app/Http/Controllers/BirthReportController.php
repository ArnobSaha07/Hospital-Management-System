<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBirthReportRequest;
use App\Http\Requests\UpdateBirthReportRequest;
use App\Models\BirthReport;
use App\Models\DeathReport;
use App\Models\PatientCase;
use App\Repositories\BirthReportRepository;
use Carbon\Carbon;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BirthReportController extends AppBaseController
{
    /** @var BirthReportRepository */
    private $birthReportRepository;

    public function __construct(BirthReportRepository $birthReportRepo)
    {
        $this->middleware('check_menu_access');
        $this->birthReportRepository = $birthReportRepo;
    }

    /**
     * Display a listing of the BirthReport.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $cases = $this->birthReportRepository->getCases();
        $doctors = $this->birthReportRepository->getDoctors();

        return view('birth_reports.index', compact('cases', 'doctors'));
    }

    /**
     * Store a newly created BirthReport in storage.
     *
     * @return JsonResponse|Redirector
     */
    public function store(CreateBirthReportRequest $request)
    {
        $input = $request->all();
        $input['date'] = Carbon::parse($input['date'])->format('Y-m-d H:i:s');
        $patientId = PatientCase::with('patient.user')->whereCaseId($input['case_id'])->first();
        $birthDate = $patientId->patient->user->dob;
        $selectBirthDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $selectBirthDate < $birthDate) {
            return $this->sendError(__('messages.flash.date_smaller'));
        }

        $isUserHasDead = DeathReport::whereCaseId($input['case_id'])->first();
        if (! empty($isUserHasDead)) {
            return $this->sendError(__('messages.flash.cant_create'));
        }
        $birthReport = $this->birthReportRepository->store($input);

        return $this->sendSuccess(__('messages.flash.birth_report_saved'));
    }

    /**
     * Display the specified BirthReport.
     *
     * @return Factory|View
     */
    public function show(BirthReport $birthReport)
    {
        if (! canAccessRecord(BirthReport::class, $birthReport->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $cases = $this->birthReportRepository->getCases();
        $doctors = $this->birthReportRepository->getDoctors();

        return view('birth_reports.show')->with([
            'birthReport' => $birthReport, 'cases' => $cases, 'doctors' => $doctors,
        ]);
    }

    /**
     * Show the form for editing the specified BirthReport.
     */
    public function edit(BirthReport $birthReport): JsonResponse
    {
        if (! canAccessRecord(BirthReport::class, $birthReport->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = BirthReport::whereId($birthReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return $this->sendError(__('messages.flash.not_allow_access_record'));
            }
        }

        return $this->sendResponse($birthReport, __('messages.flash.birth_report_retrieved'));
    }

    /**
     * Update the specified BirthReport in storage.
     */
    public function update(BirthReport $birthReport, UpdateBirthReportRequest $request): JsonResponse
    {
        $input = $request->all();
        $patientId = PatientCase::with('patient.user')->whereCaseId($input['case_id'])->first();
        $birthDate = $patientId->patient->user->dob;
        $selectBirthDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $selectBirthDate < $birthDate) {
            return $this->sendError(__('messages.flash.date_smaller'));
        }
        $birthReport = $this->birthReportRepository->update($request->all(), $birthReport);

        return $this->sendSuccess(__('messages.flash.date_smaller'));
    }

    /**
     * Remove the specified BirthReport from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(BirthReport $birthReport): JsonResponse
    {
        if (! canAccessRecord(BirthReport::class, $birthReport->id)) {
            return $this->sendError(__('messages.flash.birth_report_not_found'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = BirthReport::whereId($birthReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return $this->sendError(__('messages.flash.birth_report_not_found'));
            }
        }

        $this->birthReportRepository->delete($birthReport->id);

        return $this->sendSuccess(__('messages.flash.birth_report_deleted'));
    }
}
