<?php

namespace App\Http\Controllers;

use App\Exports\DoctorOPDChargeExport;
use App\Http\Requests\CreateDoctorOPDChargeRequest;
use App\Http\Requests\UpdateDoctorOPDChargeRequest;
use App\Models\DoctorOPDCharge;
use App\Repositories\DoctorOPDChargeRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DoctorOPDChargeController extends AppBaseController
{
    /**
     * @var DoctorOPDChargeRepository
     */
    private $doctorOPDChargeRepository;

    public function __construct(DoctorOPDChargeRepository $doctorOPDChargeRepository)
    {
        $this->doctorOPDChargeRepository = $doctorOPDChargeRepository;
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
        $doctors = $this->doctorOPDChargeRepository->getDoctors();

        return view('doctor_opd_charges.index', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDoctorOPDChargeRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $this->doctorOPDChargeRepository->create($input);

        return $this->sendSuccess(__('messages.flash.OPD_charge_saved'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DoctorOPDCharge $doctorOPDCharge): JsonResponse
    {
        if (! canAccessRecord(DoctorOPDCharge::class, $doctorOPDCharge->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($doctorOPDCharge, __('messages.flash.OPD_charge_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorOPDChargeRequest $request, DoctorOPDCharge $doctorOPDCharge): JsonResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $this->doctorOPDChargeRepository->update($input, $doctorOPDCharge->id);

        return $this->sendSuccess(__('messages.flash.OPD_charge_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws \Exception
     */
    public function destroy(DoctorOPDCharge $doctorOPDCharge): JsonResponse
    {
        if (! canAccessRecord(DoctorOPDCharge::class, $doctorOPDCharge->id)) {
            return $this->sendError(__('messages.flash.doctor_opd_charge_not_found'));
        }

        $doctorOPDCharge->delete();

        return $this->sendSuccess(__('messages.flash.OPD_charge_deleted'));
    }

    public function doctorOPDChargeExport(): BinaryFileResponse
    {
        $response = Excel::download(new DoctorOPDChargeExport, 'doctor-opd-charges-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
