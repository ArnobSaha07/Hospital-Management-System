<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDeathReportRequest;
use App\Http\Requests\UpdateDeathReportRequest;
use App\Models\DeathReport;
use App\Models\PatientCase;
use App\Repositories\DeathReportRepository;
use Carbon\Carbon;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DeathReportController extends AppBaseController
{
    /** @var DeathReportRepository */
    private $deathReportRepository;

    public function __construct(DeathReportRepository $deathReportRepo)
    {
        $this->middleware('check_menu_access');
        $this->deathReportRepository = $deathReportRepo;
    }

    /**
     * Display a listing of the DeathReport.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $cases = $this->deathReportRepository->getCases();
        $doctors = $this->deathReportRepository->getDoctors();

        return view('death_reports.index', compact('cases', 'doctors'));
    }

    /**
     * Store a newly created DeathReport in storage.
     */
    public function store(CreateDeathReportRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['date'] = Carbon::parse($input['date'])->format('Y-m-d H:i:s');
        $patientId = PatientCase::with('patient.patientUser')->whereCaseId($input['case_id'])->first();
        $birthDate = $patientId->patient->patientUser->dob;
        $deathDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $deathDate < $birthDate) {
            return $this->sendError(__('messages.flash.date_smaller'));
        }
        $deathReport = $this->deathReportRepository->store($input);

        return $this->sendSuccess(__('messages.flash.death_report_saved'));
    }

    /**
     * Display the specified DeathReport.
     *
     * @return Factory|View
     */
    public function show(DeathReport $deathReport)
    {
        if (! canAccessRecord(DeathReport::class, $deathReport->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $cases = $this->deathReportRepository->getCases();
        $doctors = $this->deathReportRepository->getDoctors();

        return view('death_reports.show')->with([
            'deathReport' => $deathReport, 'cases' => $cases, 'doctors' => $doctors,
        ]);
    }

    /**
     * Show the form for editing the specified DeathReport.
     */
    public function edit(DeathReport $deathReport): JsonResponse
    {
        if (! canAccessRecord(DeathReport::class, $deathReport->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = DeathReport::whereId($deathReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return $this->sendError(__('messages.flash.death_report_not_found'));
            }
        }

        return $this->sendResponse($deathReport, __('messages.flash.death_report_retrieved'));
    }

    /**
     * Update the specified DeathReport in storage.
     */
    public function update(DeathReport $deathReport, UpdateDeathReportRequest $request): JsonResponse
    {
        $input = $request->all();
        $patientId = PatientCase::with('patient.patientUser')->whereCaseId($input['case_id'])->first();
        $birthDate = $patientId->patient->patientUser->dob;
        $deathDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $deathDate < $birthDate) {
            return $this->sendError(__('messages.flash.date_smaller'));
        }

        $deathReport = $this->deathReportRepository->update($request->all(), $deathReport);

        return $this->sendSuccess(__('messages.flash.death_report_updated'));
    }

    /**
     * Remove the specified DeathReport from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(DeathReport $deathReport): JsonResponse
    {
        if (! canAccessRecord(DeathReport::class, $deathReport->id)) {
            return $this->sendError(__('messages.flash.death_report_not_found'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = DeathReport::whereId($deathReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return $this->sendError(__('messages.flash.death_report_not_found'));
            }
        }

        $this->deathReportRepository->delete($deathReport->id);

        return $this->sendSuccess(__('messages.flash.death_report_deleted'));
    }
}
