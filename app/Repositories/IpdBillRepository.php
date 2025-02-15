<?php

namespace App\Repositories;

use App\Models\BedAssign;
use App\Models\Doctor;
use App\Models\IpdBill;
use App\Models\IpdCharge;
use App\Models\IpdPatientDepartment;
use App\Models\IpdPayment;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IpdBillRepository
 */
class IpdBillRepository extends BaseRepository
{
    /**
     * @var string[]
     */
    protected $fieldSearchable = [
        'total_payments',
        'gross_total',
        'discount_in_percentage',
        'tax_in_percentage',
        'other_charges',
        'net_payable_amount',
    ];

    /**
     * @return array|string[]
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return IpdBill::class;
    }

    public function saveBill($input): Model
    {
        if (isset($input['bill_id'])) {
            $bill = $this->update($input, $input['bill_id']);

            return $bill;
        }
        $input['net_payable_amount'] = $input['net_payable_amount'];
        $input['other_charges'] = removeCommaFromNumbers($input['other_charges']);
        $bill = $this->create($input);
        $ipdPatientDepartment = IpdPatientDepartment::with('bedAssign')->findOrFail($bill->ipd_patient_department_id);
        $patient = Patient::with('user')->where('id', $ipdPatientDepartment->patient_id)->first();
        $ipdPatientDepartment->bill_status = 1;
        $doctor = Doctor::with('user')->where('id', $ipdPatientDepartment->doctor_id)->first();
        $receptionists = Receptionist::pluck('user_id', 'id')->toArray();
        $userIds = [
            $doctor->user_id => Notification::NOTIFICATION_FOR[Notification::DOCTOR],
            $patient->user_id => Notification::NOTIFICATION_FOR[Notification::PATIENT],
        ];

        foreach ($receptionists as $key => $userId) {
            $userIds[$userId] = Notification::NOTIFICATION_FOR[Notification::RECEPTIONIST];
        }
        $adminUser = User::role('Admin')->first();
        $allUsers = $userIds + [$adminUser->id => Notification::NOTIFICATION_FOR[Notification::ADMIN]];
        $users = getAllNotificationUser($allUsers);

        foreach ($users as $key => $notification) {
            addNotification([
                Notification::NOTIFICATION_TYPE['IPD Patient'],
                $key,
                $notification,
                $patient->user->full_name.', has been discharge.',
            ]);
        }
        $ipdPatientDepartment->save();

        $bedAssign = BedAssign::where('ipd_patient_department_id',$bill->ipd_patient_department_id)->first();
        if($bedAssign){
            $bedAssign->update(['status' => 0]);
        }

        $ipdPatientDepartment->bed->update(['is_available' => 1]);
        $ipdPatientDepartment->update(['is_discharge' => 1]);
        if ($ipdPatientDepartment->bedAssign) {
            BedAssign::where('id', $ipdPatientDepartment->bedAssign->id)->delete();
        }

        return $bill;
    }

    /**
     * @return mixed
     */
    public function getBillList($ipdPatientDepartment)
    {
        $bill['charges'] = IpdCharge::with(['chargecategory'])
            ->whereIpdPatientDepartmentId($ipdPatientDepartment->id)->get();
        $bill['payments'] = IpdPayment::whereIpdPatientDepartmentId($ipdPatientDepartment->id)->get();
        // $bedAssign = BedAssign::with(['bed'])->whereIpdPatientDepartmentId($ipdPatientDepartment->id)->first();
        // $bill['bedCharge'] = isset($bedAssign) ? $bedAssign->bed->charge : 0;

        $bed = IpdPatientDepartment::with('bed')->find($ipdPatientDepartment->id);
        $bill['bedCharge'] = $bed->bed->charge;

        $bill['ipd_patient_department'] = $ipdPatientDepartment;
        $ipdPatientBill = $ipdPatientDepartment->bill;
        if ($ipdPatientBill) {
            $bill['last_net_payable_amount'] = ($ipdPatientBill->net_payable_amount > 0) ? $ipdPatientBill->net_payable_amount : 0;
        }

        //calculate bill
        $discountInPercentage = (! is_null($ipdPatientBill)) ? $ipdPatientBill->discount_in_percentage : 0;
        $taxInPercentage = (! is_null($ipdPatientBill)) ? $ipdPatientBill->tax_in_percentage : 0;
        $otherChareges = (! is_null($ipdPatientBill)) ? $ipdPatientBill->other_charges : 0;

        $totalCharges = $bill['charges']->sum('applied_charge');
        $totalPayment = $bill['payments']->sum('amount');

        //patient side payment amount
        $bill['patient_net_payable_amount'] = ($ipdPatientBill) ? $ipdPatientBill->net_payable_amount : $totalCharges - $totalPayment;

        $totalTaxs = ($taxInPercentage / 100) * ($totalCharges - $totalPayment);
        $totalDiscount = ($discountInPercentage / 100) * $totalCharges;

        $netPayableAmount = ($totalCharges + $otherChareges + $totalTaxs + $bill['bedCharge']) - ($totalPayment + $totalDiscount);
        $grossTotal = $totalCharges - $totalPayment + $bill['bedCharge'];

        $bill['total_charges'] = $totalCharges;
        $bill['total_payment'] = $totalPayment;
        $bill['gross_total'] = $grossTotal;
        $bill['discount_in_percentage'] = $discountInPercentage;
        $bill['tax_in_percentage'] = $taxInPercentage;
        $bill['other_charges'] = $otherChareges;
        $bill['net_payable_amount'] = number_format($netPayableAmount);

        return $bill;
    }

    /**
     * @return mixed
     */
    public function getSyncListForCreate()
    {
        $data['setting'] = Setting::pluck('value', 'key');

        return $data;
    }
}
