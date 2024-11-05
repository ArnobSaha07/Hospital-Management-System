<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\IpdPatientDepartment;
use App\Models\IpdPayment;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\IpdBillRepository;
use App\Repositories\IpdPatientDepartmentRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class IpdPatientDepartmentController extends Controller
{
    /** @var IpdPatientDepartmentRepository */
    private $ipdPatientDepartmentRepository;

    public function __construct(IpdPatientDepartmentRepository $ipdPatientDepartmentRepo)
    {
        $this->ipdPatientDepartmentRepository = $ipdPatientDepartmentRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('ipd_patient_list.index');
    }

    /**
     * Display the specified resource.
     *
     * @return Factory|View
     */
    public function show(IpdPatientDepartment $ipdPatientDepartment)
    {
        if (! canAccessRecord(IpdPatientDepartment::class, $ipdPatientDepartment->id)) {
            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $ipdPatientDepartment->patient_id) {
                return Redirect::back();
            }
        }

        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $stripeKeyValue = Setting::whereTenantId($tenantId)->where('key', '=', 'stripe_key')->first();
        if(isset($stripeKeyValue->value) && !empty($stripeKeyValue)){
            $stripeKey = $stripeKeyValue->value;
        }else{
            $stripeKey = null;
        }
        $paymentModes = IpdPayment::NEW_PAYMENT_MODES;
        $ipdPatientDepartmentRepository = \App::make(IpdBillRepository::class);
        $bill = $ipdPatientDepartmentRepository->getBillList($ipdPatientDepartment);

        return view('ipd_patient_list.show', compact('ipdPatientDepartment', 'paymentModes', 'bill', 'stripeKey'));
    }
}
