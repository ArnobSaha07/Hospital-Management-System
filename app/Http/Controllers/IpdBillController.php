<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdBillRequest;
use App\Models\IpdConsultantRegister;
use App\Models\IpdDiagnosis;
use App\Models\IpdPatientDepartment;
use App\Models\IpdPrescription;
use App\Repositories\IpdBillRepository;
use \PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class IpdBillController extends AppBaseController
{
    /** @var IpdBillRepository */
    private $ipdBillRepository;

    public function __construct(IpdBillRepository $ipdBillRepo)
    {
        $this->ipdBillRepository = $ipdBillRepo;
    }

    /**
     * Store a newly created Bill in storage.
     *
     *
     * @throws Exception
     */
    public function store(CreateIpdBillRequest $request)
    {
        $input = $request->all();
        $bill = $this->ipdBillRepository->saveBill($input);

        return $this->sendResponse($bill, __('messages.flash.IPD_bill_saved'));
    }

    public function ipdBillConvertToPdf(IpdPatientDepartment $ipdPatientDepartment)
    {
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $data = $this->ipdBillRepository->getSyncListForCreate();

        $data['bill'] = $this->ipdBillRepository->getBillList($ipdPatientDepartment);
        $data['currencySymbol'] = getCurrencySymbol();
        $pdf = PDF::loadView('ipd_bills.bill_pdf', $data);

        return $pdf->stream('bill.pdf');
    }

    public function ipdDischargePatientToPdf(IpdPatientDepartment $ipdPatientDepartment)
    {
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $data = $this->ipdBillRepository->getSyncListForCreate();

        $data['bill'] = $this->ipdBillRepository->getBillList($ipdPatientDepartment);
        $data['diagnosis'] = IpdDiagnosis::whereIpdPatientDepartmentId($ipdPatientDepartment->id)->get();
        $data['instructions'] = IpdConsultantRegister::with('doctor.doctorUser')->where('ipd_patient_department_id',$ipdPatientDepartment->id)->get();
        $data['ipdPrescriptions'] = IpdPrescription::with(['patient','ipdPrescriptionItems'])->where('ipd_patient_department_id', $ipdPatientDepartment->id)->get();

        $data['currencySymbol'] = getCurrencySymbol();
        $pdf = PDF::loadView('ipd_bills.discharge_slip', $data);

        return $pdf->stream('discharge.pdf');
    }
}
