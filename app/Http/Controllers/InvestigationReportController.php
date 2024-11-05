<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvestigationReportRequest;
use App\Http\Requests\UpdateInvestigationReportRequest;
use App\Models\InvestigationReport;
use App\Models\Patient;
use App\Repositories\InvestigationReportRepository;
use Carbon\Carbon;
use Exception;
use Flash;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;
use Str;

class InvestigationReportController extends AppBaseController
{
    /** @var InvestigationReportRepository */
    private $investigationReportRepository;

    public function __construct(InvestigationReportRepository $investigationReportRepo)
    {
        $this->middleware('check_menu_access');
        $this->investigationReportRepository = $investigationReportRepo;
    }

    /**
     * Display a listing of the InvestigationReport.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = InvestigationReport::STATUS_ARR;

        return view('investigation_reports.index', $data);
    }

    /**
     * Show the form for creating a new InvestigationReport.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $status = InvestigationReport::STATUS;
        $patients = $this->investigationReportRepository->getPatients();
        $doctors = $this->investigationReportRepository->getDoctors();

        return view('investigation_reports.create', compact('status', 'patients', 'doctors'));
    }

    /**
     * Store a newly created InvestigationReport in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateInvestigationReportRequest $request): RedirectResponse
    {
        $input = $request->all();
        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->patientUser->dob;
        $reportDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $reportDate < $birthDate) {
            Flash::error(__('messages.flash.investigation_date_smaller'));

            return redirect()->back()->withInput($input);
        }
        $this->investigationReportRepository->store($input);
        Flash::success(__('messages.flash.investigation_report_saved'));

        return redirect(route('investigation-reports.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse|Redirector
     */
    public function show(InvestigationReport $investigationReport)
    {
        if (! canAccessRecord(InvestigationReport::class, $investigationReport->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('investigation_reports.show')->with('investigationReport', $investigationReport);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(InvestigationReport $investigationReport)
    {
        if (! canAccessRecord(InvestigationReport::class, $investigationReport->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = InvestigationReport::whereId($investigationReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return Redirect::back();
            }
        }

        $status = InvestigationReport::STATUS;
        $patients = $this->investigationReportRepository->getPatients();
        $doctors = $this->investigationReportRepository->getDoctors();
        $fileExt = pathinfo($investigationReport->attachment_url, PATHINFO_EXTENSION);

        return view('investigation_reports.edit',
            compact('investigationReport', 'status', 'patients', 'doctors', 'fileExt'));
    }

    /**
     * Update the specified InvestigationReport in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(InvestigationReport $investigationReport, UpdateInvestigationReportRequest $request): RedirectResponse
    {
        if (empty($investigationReport)) {
            Flash::error(__('messages.flash.investigation_report_not_found'));

            return redirect(route('investigation-reports.index'));
        }
        $input = $request->all();
        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->patientUser->dob;
        $reportDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $reportDate < $birthDate) {
            Flash::error(__('messages.flash.investigation_date_smaller'));

            return redirect()->back()->withInput($input);
        }
        $this->investigationReportRepository->update($input, $investigationReport->id);
        Flash::success(__('messages.flash.investigation_report_updated'));

        return redirect(route('investigation-reports.index'));
    }

    /**
     * Remove the specified InvestigationReport from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(InvestigationReport $investigationReport): JsonResponse
    {
        if (! canAccessRecord(InvestigationReport::class, $investigationReport->id)) {
            return $this->sendError(__('messages.flash.investigation_report_not_found'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientCaseHasDoctor = InvestigationReport::whereId($investigationReport->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientCaseHasDoctor) {
                return $this->sendError(__('messages.flash.investigation_report_not_found'));
            }
        }

        $investigationReport->delete();

        return $this->sendSuccess(__('messages.flash.investigation_report_deleted'));
    }

    /**
     * @return ResponseFactory|\Illuminate\Http\Response
     *
     * @throws FileNotFoundException
     */
    public function downloadMedia(InvestigationReport $investigationReport): Response
    {
        /** @var Media $documentMedia */
        $documentMedia = $investigationReport->media[0];
        $documentPath = $documentMedia->getPath();
        if (config('app.media_disc') === 'public') {
            $documentPath = (Str::after($documentMedia->getUrl(), '/uploads'));
        }

        ob_end_clean();

        $file = Storage::disk(config('app.media_disc'))->get($documentPath);

        $headers = [
            'Content-Type' => $investigationReport->media[0]->mime_type,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$investigationReport->media[0]->file_name}",
            'filename' => $investigationReport->media[0]->file_name,
        ];

        return response($file, 200, $headers);
    }
}
