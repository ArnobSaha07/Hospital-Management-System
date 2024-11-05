<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdConsultantRegisterRequest;
use App\Http\Requests\UpdateIpdConsultantRegisterRequest;
use App\Models\IpdConsultantRegister;
use App\Repositories\IpdConsultantRegisterRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;

class IpdConsultantRegisterController extends AppBaseController
{
    /** @var IpdConsultantRegisterRepository */
    private $ipdConsultantRegisterRepository;

    public function __construct(IpdConsultantRegisterRepository $ipdConsultantRegisterRepo)
    {
        $this->ipdConsultantRegisterRepository = $ipdConsultantRegisterRepo;
    }

    /**
     * Display a listing of the IpdConsultantRegister.
     *
     *
     * @throws Exception
     */
    public function index(Request $request): Response
    {
    }

    /**
     * Store a newly created IpdConsultantRegister in storage.
     */
    public function store(CreateIpdConsultantRegisterRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->ipdConsultantRegisterRepository->store($input);

        return $this->sendSuccess(__('messages.flash.IPD_consultant_saved'));
    }

    /**
     * Show the form for editing the specified IpdPrescription.
     */
    public function edit(IpdConsultantRegister $ipdConsultantRegister): JsonResponse
    {
        if (! canAccessRecord(IpdConsultantRegister::class, $ipdConsultantRegister->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($ipdConsultantRegister, __('messages.flash.IPD_consultant_retrieved'));
    }

    /**
     * Update the specified IpdPrescriptionItem in storage.
     */
    public function update(IpdConsultantRegister $ipdConsultantRegister, UpdateIpdConsultantRegisterRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->ipdConsultantRegisterRepository->update($input, $ipdConsultantRegister->id);

        return $this->sendSuccess(__('messages.flash.IPD_consultant_updated'));
    }

    /**
     * Remove the specified IpdConsultantRegister from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(IpdConsultantRegister $ipdConsultantRegister): JsonResponse
    {
        if (! canAccessRecord(IpdConsultantRegister::class, $ipdConsultantRegister->id)) {
            return $this->sendError(__('messages.flash.ipd_consultant_register_not_found'));
        }

        $ipdConsultantRegister->delete();

        return $this->sendSuccess(__('messages.flash.IPD_consultant_deleted'));
    }
}
