<?php

namespace App\Repositories;

use App;
use App\Models\Bed;
use App\Models\BedAssign;
use App\Models\BedType;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\IpdPatientDepartment;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\PatientCase;
use App\Models\Prescription;
use App\Models\Setting;
use DB;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class IpdPatientDepartmentRepository
 *
 * @version September 8, 2020, 6:42 am UTC
 */
class IpdPatientDepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'patient_id',
        'ipd_number',
        'height',
        'weight',
        'bp',
        'symptoms',
        'notes',
        'admission_date',
        'case_id',
        'is_old_patient',
        'doctor_id',
        'bed_group_id',
        'bed_id',
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
        return IpdPatientDepartment::class;
    }

    /**
     * @return mixed
     */
    public function getAssociatedData()
    {
        $data['patients'] = Patient::with('patientUser')->get()->where('patientUser.status', '=', 1)->pluck('patientUser.full_name',
            'id')->sort();
        $data['doctors'] = Doctor::with('doctorUser')->get()->where('doctorUser.status', '=', 1)->pluck('doctorUser.full_name',
            'id')->sort();
        $data['bedTypes'] = BedType::pluck('title', 'id')->toArray();
        natcasesort($data['bedTypes']);
        $data['ipdNumber'] = $this->model->generateUniqueIpdNumber();

        return $data;
    }

    public function getPatientCases(int $patientId): Collection
    {
        // $ipdPatient = IpdPatientDepartment::pluck('case_id')->toArray();
        $patientCase = PatientCase::where('patient_id', $patientId)->where('status', 1)->get()->pluck('case_id', 'id');
        return $patientCase;
    }

    public function getPatientBeds(int $bedTypeId, $isEdit, $bedId, $ipdPatientBedTypeId): Collection
    {
        $beds = null;

        if (! $isEdit) {
            $beds = Bed::orderBy('name')->where('bed_type', $bedTypeId)->where('is_available', 1)->pluck('name', 'id');
        } else {
            $beds = Bed::orderBy('name')->where('bed_type', $bedTypeId)->where('is_available', 1);
            if ($bedTypeId == $ipdPatientBedTypeId) {
                $beds->orWhere('id', $bedId);
            }
            $beds = $beds->pluck('name', 'id');
        }

        return $beds;
    }

    public function getDoctorsData(): Collection
    {
        return Doctor::with('doctorUser')->get()->where('doctorUser.status', '=', 1)->pluck('doctorUser.full_name', 'id');
    }

    public function getPatientsData(): Collection
    {
        return Patient::with('patientUser')->get()->where('patientUser.status', '=', 1)->pluck('patientUser.full_name', 'id');
    }

    public function getDoctorsList(): array
    {
        $result = Doctor::with('doctorUser')->get()
            ->where('doctorUser.status', '=', 1)->pluck('doctorUser.full_name', 'id')->toArray();

        $doctors = [];
        foreach ($result as $key => $item) {
            $doctors[] = [
                'key' => $key,
                'value' => $item,
            ];
        }

        return $doctors;
    }

    public function getMedicinesCategoriesData(): Collection
    {
        return Category::where('is_active', '=', 1)->pluck('name', 'id');
    }

    public function getMedicineCategoriesList(): array
    {
        $result = Category::where('is_active', '=', 1)->pluck('name', 'id')->toArray();

        $medicineCategories = [];
        foreach ($result as $key => $item) {
            $medicineCategories[] = [
                'key' => $key,
                'value' => $item,
            ];
        }

        return $medicineCategories;
    }

    public function store(array $input): bool
    {
        try {
            $input['is_old_patient'] = isset($input['is_old_patient']) ? true : false;
            $jsonFields = [];

            foreach ($input as $key => $value) {
                if (strpos($key, 'field') === 0) {
                    $jsonFields[$key] = $value;
                }
            }
            $input['custom_field'] = !empty($jsonFields) ? $jsonFields : null;
            $ipdPatientDepartment = IpdPatientDepartment::create($input);

            $bedAssignData = [
                'bed_id' => $input['bed_id'],
                'patient_id' => $input['patient_id'],
                'case_id' => $ipdPatientDepartment->patientCase->case_id,
                'assign_date' => $input['admission_date'],
                'ipd_patient_department_id' => $ipdPatientDepartment->id,
                'status' => true,
            ];
            /** @var BedAssignRepository $bedAssign */
            $bedAssign = App::make(BedAssignRepository::class);
            $bedAssign->store($bedAssignData);

        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    public function updateIpdPatientDepartment(array $input, IpdPatientDepartment $ipdPatientDepartment): bool
    {
        try {
            DB::beginTransaction();
            $input['is_old_patient'] = isset($input['is_old_patient']) ? true : false;
            $bedId = $ipdPatientDepartment->bed_id;
            $jsonFields = [];

            foreach ($input as $key => $value) {
                if (strpos($key, 'field') === 0) {
                    $jsonFields[$key] = $value;
                }
            }
            $input['custom_field'] = !empty($jsonFields) ? $jsonFields : null;

            /** @var IpdPatientDepartment $ipdPatientDepartment */
            $ipdPatientDepartment = $this->update($input, $ipdPatientDepartment->id);

            $bedAssignData = [
                'bed_id' => $input['bed_id'],
                'patient_id' => $input['patient_id'],
                'case_id' => $ipdPatientDepartment->patientCase->case_id,
                'assign_date' => $input['admission_date'],
                'status' => true,
            ];

            /** @var BedAssign $bedAssign */
            $bedAssignUpdate = BedAssign::whereBedId($bedId)->latest()->first();

            // if (! empty($bedAssignUpdate)) {
            //     /** @var BedAssignRepository $bedAssign */
            //     $bedAssign = App::make(BedAssignRepository::class);
            //     $bedAssign->update($bedAssignData, $bedAssignUpdate);
            // }
            $bedAssign = App::make(BedAssignRepository::class);

            if (empty($bedAssignUpdate)) {
                $bedAssigns = BedAssign::create($bedAssignData);
                BedAssign::where('id',$bedAssigns->id)->update(['ipd_patient_department_id' => $ipdPatientDepartment->id]);
            } else {
                $bedAssign->update($bedAssignData, $bedAssignUpdate);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function deleteIpdPatientDepartment(IpdPatientDepartment $ipdPatientDepartment): bool
    {
        $ipdPatientDepartment->bed->update(['is_available' => 1]);
        if ($ipdPatientDepartment->bedAssign) {
            BedAssign::where('id', $ipdPatientDepartment->bedAssign->id)->delete();
        }
        $ipdPatientDepartment->delete();

        return true;
    }

    /**
     * @return mixed
     */
    public function getSyncListForCreate()
    {
        $data['setting'] = Setting::pluck('value', 'key');

        return $data;
    }

    public function createNotification(array $input)
    {
        try {
            $patient = Patient::with('patientUser')->where('id', $input['patient_id'])->first();
            addNotification([
                Notification::NOTIFICATION_TYPE['IPD Patient'],
                $patient->user_id,
                Notification::NOTIFICATION_FOR[Notification::PATIENT],
                $patient->patientUser->full_name.' your IPD record has been created.',
            ]);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function getDoseDurationList()
    {
        $result = Prescription::DOSE_DURATION;

        $doseDuration = [];
        foreach ($result as $key => $item) {
            $doseDuration[] = [
                'key' => $key,
                'value' => $item,
            ];
        }

        return $doseDuration;
    }

    public function getDoseIntervalList()
    {
        $result = Prescription::DOSE_INTERVAL;

        $doseInterval = [];
        foreach ($result as $key => $item) {
            $doseInterval[] = [
                'key' => $key,
                'value' => $item,
            ];
        }

        return $doseInterval;

    }

    public function getMealList()
    {
        $result = Prescription::MEAL_ARR;

        $meal = [];
        foreach ($result as $key => $item) {
            $meal[] = [
                'key' => $key,
                'value' => $item,
            ];
        }

        return $meal;
    }
}
