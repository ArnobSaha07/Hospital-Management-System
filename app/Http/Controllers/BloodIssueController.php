<?php

namespace App\Http\Controllers;

use App\Exports\BloodIssueExport;
use App\Http\Requests\BloodIssueRequest;
use App\Models\BloodDonor;
use App\Models\BloodIssue;
use App\Repositories\BloodIssueRepository;
use App\Repositories\PatientCaseRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class BloodIssueController
 */
class BloodIssueController extends AppBaseController
{
    /** @var BloodIssueRepository */
    private $bloodIssueRepository;

    /** @var PatientCaseRepository */
    private $patientCaseRepository;

    /**
     * BloodIssueController constructor.
     */
    public function __construct(
        BloodIssueRepository $bloodIssueRepository,
        PatientCaseRepository $patientCaseRepository
    ) {
        $this->middleware('check_menu_access');
        $this->bloodIssueRepository = $bloodIssueRepository;
        $this->patientCaseRepository = $patientCaseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $doctors = $this->patientCaseRepository->getDoctors();
        $patients = $this->patientCaseRepository->getPatients();
        $donors = BloodDonor::orderBy('name')->pluck('name', 'id');

        return view('blood_issues.index', compact('doctors', 'patients', 'donors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BloodIssueRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            $input['amount'] = removeCommaFromNumbers($input['amount']);
            $this->bloodIssueRepository->create($input);

            return $this->sendSuccess(__('messages.flash.blood_issue_saved'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function getBloodGroup(Request $request): JsonResponse
    {
        try {
            $bloodGroup = $this->bloodIssueRepository->getBloodGroup($request->get('id'));

            return $this->sendResponse($bloodGroup, __('messages.flash.blood_group_retrieved'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BloodIssue $bloodIssue): JsonResponse
    {
        if (! canAccessRecord(BloodIssue::class, $bloodIssue->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($bloodIssue, __('messages.flash.blood_issue_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BloodIssueRequest $request, BloodIssue $bloodIssue): JsonResponse
    {
        try {
            $input = $request->all();
            $input['amount'] = removeCommaFromNumbers($input['amount']);
            $this->bloodIssueRepository->update($input, $bloodIssue->id);

            return $this->sendSuccess(__('messages.flash.blood_issue_updated'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(BloodIssue $bloodIssue): JsonResponse
    {
        if (! canAccessRecord(BloodIssue::class, $bloodIssue->id)) {
            return $this->sendError(__('messages.flash.blood_issue_not_found'));
        }

        try {
            $bloodIssue->delete();

            return $this->sendSuccess(__('messages.flash.blood_issue_deleted'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function export(): BinaryFileResponse
    {
        $response = Excel::download(new BloodIssueExport, 'blood-issue-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
