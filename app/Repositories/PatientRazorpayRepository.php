<?php

namespace App\Repositories;

use App\Models\IpdPatientDepartment;
use App\Models\IpdPayment;
use App\Models\Transaction;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class PatientRazorpayRepository
 */
class PatientRazorpayRepository
{
    /**
     * @throws \Throwable
     */
    public function patientPaymentSuccess($payment)
    {
        try {
            DB::beginTransaction();

            $userId = getLoggedInUserId();
            $amount = $payment['amount'] / 100;
            $transactionID = $payment['id'];
            $ipdPatientId = $payment['notes']['ipd_patient_id'];

            $transactionData = [
                'transaction_id' => $transactionID,
                'amount' => $amount,
                'user_id' => $userId,
                'status' => 'paid',
                'meta' => $payment,
            ];

            $transaction = Transaction::create($transactionData);

            $ipdPaymentData = [
                'transaction_id' => $transaction->id,
                'ipd_patient_department_id' => $ipdPatientId,
                'payment_mode' => IpdPayment::PAYMENT_MODES_RAZORPAY,
                'date' => Carbon::now(),
                'notes' => $payment['notes']['notes'],
                'amount' => $amount,
            ];

            $ipdPayment = App::make(IpdPaymentRepository::class);
            $ipdPayment->store($ipdPaymentData);

            // update ipd bill
            $ipdPatientDepartment = IpdPatientDepartment::findOrFail($ipdPatientId);
            $ipdBill = $ipdPatientDepartment->bill;
            if ($ipdBill) {
                $ipdBill->total_payments = $ipdBill->total_payments + $amount;
                $ipdBill->net_payable_amount = $ipdBill->net_payable_amount - $amount;
                $ipdBill->save();

                $ipdPatientDepartment->bill_status = 1;
                $ipdPatientDepartment->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

    }
}
