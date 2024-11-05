<?php

namespace App\Repositories;

use App\Models\IpdPatientDepartment;
use App\Models\IpdPrescription;
use App\Models\IpdPrescriptionItem;
use App\Models\Medicine;
use App\Models\MedicineBill;
use App\Models\Notification;
use App\Models\OpdPatientDepartment;
use App\Models\OpdPrescription;
use App\Models\OpdPrescriptionItem;
use App\Models\SaleMedicine;
use Arr;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class IpdPrescriptionRepository
 *
 * @version September 10, 2020, 11:42 am UTC
 */
class OpdPrescriptionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'opd_patient_department_id',
        'created_at',
    ];

    /**
     * Return searchable fields
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
    **/
    public function model()
    {
        return OpdPrescription::class;
    }

    public function getMedicines(int $medicineCategoryId): Collection
    {
        return Medicine::where('category_id', $medicineCategoryId)->pluck('name', 'id');
    }

    public function store(array $input): bool
    {
        try {
            $opdPrescriptionArr = Arr::only($input, $this->model->getFillable());
            $opdPrescription = $this->model->create($opdPrescriptionArr);
            $opdDepartment = OpdPatientDepartment::with('patient', 'doctor')->whereId($input['opd_patient_department_id'])->first();
            $amount = 0;
            $qty = 0;

            $medicineBill = MedicineBill::create([
                'bill_number' => 'BIL'.generateUniqueBillNumber(),
                'patient_id' => $opdDepartment->patient->id,
                'doctor_id' => $opdDepartment->doctor->id,
                'model_type' => \App\Models\OpdPrescription::class,
                'model_id' => $opdPrescription->id,
                'payment_status' => MedicineBill::UNPAID,
                'bill_date' => Carbon::now(),

            ]);
            foreach ($input['category_id'] as $key => $value) {
                $opdPrescriptionItem = [
                    'opd_prescription_id' => $opdPrescription->id,
                    'category_id' => $input['category_id'][$key],
                    'medicine_id' => $input['medicine_id'][$key],
                    'dosage' => $input['dosage'][$key],
                    'day' => $input['day'][$key],
                    'time' => $input['time'][$key],
                    'dose_interval' => $input['dose_interval'][$key],
                    'instruction' => $input['instruction'][$key],
                ];
                OpdPrescriptionItem::create($opdPrescriptionItem);

                $medicine = Medicine::find($input['medicine_id'][$key]);
                $amount += $input['day'][$key] * $input['dose_interval'][$key] * $medicine->selling_price;
                $qty = $input['day'][$key] * $input['dose_interval'][$key];
                $saleMedicineArray = [
                    'medicine_bill_id' => $medicineBill->id,
                    'medicine_id' => $medicine->id,
                    'sale_quantity' => $qty,
                    'sale_price' => $medicine->selling_price,
                    'tax' => 0,
                ];
                SaleMedicine::create($saleMedicineArray);
            }
            $medicineBill->update([
                'net_amount' => $amount,
                'total' => $amount,
            ]);

        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getOpdPrescriptionData(OpdPrescription $opdPrescription)
    {
        $data['opdPrescription'] = $opdPrescription->toArray();
        $data['opdPrescriptionItems'] = $opdPrescription->opdPrescriptionItems->toArray();
        $data['medicines'] = Medicine::pluck('name', 'id');

        return $data;
    }

    public function updateOpdPrescriptionItems(array $input, OpdPrescription $opdPrescription): bool
    {
        try {
            $medicineBill = MedicineBill::whereModelId($opdPrescription->id)->whereModelType(\App\Models\OpdPrescription::class)->first();
            $medicineBill->saleMedicine()->delete();
            $opdPrescriptionArr = Arr::only($input, $this->model->getFillable());
            $opdPrescription->update($opdPrescriptionArr);
            $opdPrescription->opdPrescriptionItems()->delete();
            $opdDepartment = OpdPatientDepartment::with('patient', 'doctor')->whereId($input['opd_patient_department_id'])->first();
            $amount = 0;
            $qty = 0;
            foreach ($input['category_id'] as $key => $value) {
                $opdPrescriptionItem = [
                    'opd_prescription_id' => $opdPrescription->id,
                    'category_id' => $input['category_id'][$key],
                    'medicine_id' => $input['medicine_id'][$key],
                    'dosage' => $input['dosage'][$key],
                    'day' => $input['day'][$key],
                    'time' => $input['time'][$key],
                    'dose_interval' => $input['dose_interval'][$key],
                    'instruction' => $input['instruction'][$key],
                ];
                OpdPrescriptionItem::create($opdPrescriptionItem);

                $medicine = Medicine::find($input['medicine_id'][$key]);
                $amount += $input['day'][$key] * $input['dose_interval'][$key] * $medicine->selling_price;
                $qty = $input['day'][$key] * $input['dose_interval'][$key];
                $saleMedicineArray = [
                    'medicine_bill_id' => $medicineBill->id,
                    'medicine_id' => $medicine->id,
                    'sale_quantity' => $qty,
                    'sale_price' => $medicine->selling_price,
                    'tax' => 0,
                ];
                SaleMedicine::create($saleMedicineArray);
            }
            $medicineBill->update([
                'net_amount' => $amount,
                'total' => $amount,
            ]);

        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    public function createNotification(array $input)
    {
        try {
            $patient = OpdPatientDepartment::with('patient.patientUser')->where('id',
                $input['opd_patient_department_id'])->first();
            $doctor = OpdPatientDepartment::with('doctor.doctorUser')->where('id',
                $input['opd_patient_department_id'])->first();
            $userIds = [
                $doctor->doctor->user_id => Notification::NOTIFICATION_FOR[Notification::DOCTOR],
                $patient->patient->user_id => Notification::NOTIFICATION_FOR[Notification::PATIENT],
            ];

            foreach ($userIds as $key => $notification) {
                if ($notification == Notification::NOTIFICATION_FOR[Notification::PATIENT]) {
                    $title = $patient->patient->patientUser->full_name.' your OPD prescription has been created.';
                } else {
                    $title = $patient->patient->patientUser->full_name.' OPD prescription has been created.';
                }
                addNotification([
                    Notification::NOTIFICATION_TYPE['OPD Prescription'],
                    $key,
                    $notification,
                    $title,
                ]);
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
