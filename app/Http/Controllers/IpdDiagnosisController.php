<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdDiagnosisRequest;
use App\Http\Requests\UpdateIpdDiagnosisRequest;
use App\Models\IpdDiagnosis;
use App\Repositories\IpdDiagnosisRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IpdDiagnosisController extends AppBaseController
{
    /** @var IpdDiagnosisRepository */
    private $ipdDiagnosisRepository;

    public function __construct(IpdDiagnosisRepository $ipdDiagnosisRepo)
    {
        $this->ipdDiagnosisRepository = $ipdDiagnosisRepo;
    }

    /**
     * Display a listing of the IpdDiagnosis.
     *
     *
     * @throws Exception
     */
    public function index(Request $request): Response
    {
    }

    /**
     * Store a newly created IpdDiagnosis in storage.
     */
    public function store(CreateIpdDiagnosisRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->ipdDiagnosisRepository->store($input);

        return $this->sendSuccess(__('messages.flash.IPD_diagnosis_saved'));
    }

    /**
     * Show the form for editing the specified Ipd Diagnosis.
     */
    public function edit(IpdDiagnosis $ipdDiagnosis): JsonResponse
    {
        if (! canAccessRecord(IpdDiagnosis::class, $ipdDiagnosis->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($ipdDiagnosis, __('messages.flash.IPD_diagnosis_retrieved'));
    }

    /**
     * Update the specified Ipd Diagnosis in storage.
     */
    public function update(IpdDiagnosis $ipdDiagnosis, UpdateIpdDiagnosisRequest $request): JsonResponse
    {
        $this->ipdDiagnosisRepository->updateIpdDiagnosis($request->all(), $ipdDiagnosis->id);

        return $this->sendSuccess(__('messages.flash.IPD_diagnosis_updated'));
    }

    /**
     * Remove the specified IpdDiagnosis from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(IpdDiagnosis $ipdDiagnosis): JsonResponse
    {
        if (! canAccessRecord(IpdDiagnosis::class, $ipdDiagnosis->id)) {
            return $this->sendError(__('messages.flash.ipd_diagnosis_not_found'));
        }

        $this->ipdDiagnosisRepository->deleteIpdDiagnosis($ipdDiagnosis->id);

        return $this->sendSuccess(__('messages.flash.IPD_diagnosis_deleted'));
    }

    public function downloadMedia(IpdDiagnosis $ipdDiagnosis): Media
    {
        $media = $ipdDiagnosis->getMedia(IpdDiagnosis::IPD_DIAGNOSIS_PATH)->first();
        ob_end_clean();
        if ($media != null) {
            $media = $media->id;
            $mediaItem = Media::findOrFail($media);

            return $mediaItem;
        }

        return '';
    }
}
