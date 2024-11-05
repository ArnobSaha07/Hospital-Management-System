<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpdDiagnosisRequest;
use App\Http\Requests\UpdateOpdDiagnosisRequest;
use App\Models\OpdDiagnosis;
use App\Repositories\OpdDiagnosisRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OpdDiagnosisController extends AppBaseController
{
    /** @var OpdDiagnosisRepository */
    private $opdDiagnosisRepository;

    public function __construct(OpdDiagnosisRepository $opdDiagnosisRepo)
    {
        $this->opdDiagnosisRepository = $opdDiagnosisRepo;
    }

    /**
     * Display a listing of the OpdDiagnosis.
     *
     *
     * @throws Exception
     */
    public function index(Request $request): Response
    {
    }

    /**
     * Store a newly created OpdDiagnosis in storage.
     */
    public function store(CreateOpdDiagnosisRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->opdDiagnosisRepository->store($input);
        $this->opdDiagnosisRepository->createNotification($input);

        return $this->sendSuccess(__('messages.flash.OPD_diagnosis_saved'));
    }

    /**
     * Show the form for editing the specified Opd Diagnosis.
     */
    public function edit(OpdDiagnosis $opdDiagnosis): JsonResponse
    {
        if (! canAccessRecord(OpdDiagnosis::class, $opdDiagnosis->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($opdDiagnosis, __('messages.flash.OPD_diagnosis_retrieved'));
    }

    /**
     * Update the specified Opd Diagnosis in storage.
     */
    public function update(OpdDiagnosis $opdDiagnosis, UpdateOpdDiagnosisRequest $request): JsonResponse
    {
        $this->opdDiagnosisRepository->updateOpdDiagnosis($request->all(), $opdDiagnosis->id);

        return $this->sendSuccess(__('messages.flash.OPD_diagnosis_updated'));
    }

    /**
     * Remove the specified OpdDiagnosis from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(OpdDiagnosis $opdDiagnosis): JsonResponse
    {
        if (! canAccessRecord(OpdDiagnosis::class, $opdDiagnosis->id)) {
            return $this->sendError(__('messages.flash.opd_diagnosis_not_found'));
        }

        $this->opdDiagnosisRepository->deleteOpdDiagnosis($opdDiagnosis->id);

        return $this->sendSuccess(__('messages.flash.OPD_diagnosis_deleted'));
    }

    public function downloadMedia(OpdDiagnosis $opdDiagnosis): Media
    {
        $media = $opdDiagnosis->getMedia(OpdDiagnosis::OPD_DIAGNOSIS_PATH)->first();
        ob_end_clean();
        if ($media) {
            return $media;
        }

        return '';
    }
}
