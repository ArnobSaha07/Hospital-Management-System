<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Bed;
use App\Models\BedAssign;
use App\Models\BedType;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\IpdPatientDepartment;
use App\Models\OpdPatientDepartment;
use App\Models\Patient;
use App\Models\PatientCase;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DefaultPatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $userTenantId = session('tenant_id', null);

        $departmentID = Department::whereName('Patient')->first()->id;

        $patientData = [
            'department_id' => $departmentID,
            'first_name' => $faker->unique()->name(),
            'last_name' => $faker->unique()->name(),
            'email' => $faker->unique()->safeEmail(),
            'password' => Hash::make('123456'),
            'designation' => 'patient',
            'status' => 1,
            'email_verified_at' => Carbon::now(),
            'tenant_id' => $userTenantId,
        ];

        $user = User::create($patientData);

        $patient = Patient::create([
            'user_id' => $user->id,
            'tenant_id' => $userTenantId,
        ]);
        $ownerId = $patient->id;
        $ownerType = Patient::class;

        $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
        $user->assignRole($departmentID);

        // appointment create

        $doctors = Doctor::where('tenant_id', $userTenantId)->get();

        foreach ($doctors as $doctor) {
            $doctorExits = Appointment::whereDoctorId($doctor->id)->exists();
            if (! $doctorExits) {
                $doctorId = $doctor['id'];
                $doctorDepartmentId = $doctor['doctor_department_id'];

                break;
            }
        }

        $appointmentDate = \Carbon\Carbon::parse(Carbon::now()->addDay())->isoFormat('Y-MM-DD');
        $appointmentOpdDate = $appointmentDate.' 08:45:00';

        $appointmentData = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctorId,
            'department_id' => $doctorDepartmentId,
            'opd_date' => $appointmentOpdDate,
            'problem' => $faker->text(20),
            'is_completed' => Appointment::STATUS_COMPLETED,
            'tenant_id' => $userTenantId,
        ];

        $appointment = Appointment::create($appointmentData);

        // patient case create

        $doctorID = Doctor::where('tenant_id', $userTenantId)->inRandomOrder()->first()->id;

        $patientCaseData = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctorID,
            'date' => Carbon::now(),
            'fee' => $faker->numberBetween(200, 500),
            'status' => 1,
            'case_id' => mb_strtoupper(PatientCase::generateUniqueCaseId()),
            'tenant_id' => $userTenantId,
        ];

        PatientCase::create($patientCaseData);

        // Ipd patient create

        $patientCase = PatientCase::where('tenant_id', $userTenantId)->wherePatientId($patient->id)->first();
        $patientCaseId = $patientCase->id;
        $bedTypeID = BedType::where('tenant_id', $userTenantId)->inRandomOrder()->first()->id;
        $bedID = Bed::where('tenant_id', $userTenantId)->whereBedType($bedTypeID)->first()->id;

        $ipdNumber = strtoupper(Str::random(8));
        while (true) {
            $isExist = IpdPatientDepartment::whereIpdNumber($ipdNumber)->exists();
            if ($isExist) {
                IpdPatientDepartment::generateUniqueIpdNumber();
            }
            break;
        }

        $ipdPatientData = [
            'patient_id' => $patient->id,
            'ipd_number' => $ipdNumber,
            'case_id' => $patientCaseId,
            'is_old_patient' => false,
            'admission_date' => Carbon::now(),
            'doctor_id' => $doctorID,
            'bed_type_id' => $bedTypeID,
            'bed_id' => $bedID,
            'tenant_id' => $userTenantId,
        ];

        $ipdPatientDepartment = IpdPatientDepartment::create($ipdPatientData);

        // Bed assign create

        $bedAssignData = [
            'bed_id' => $bedID,
            'patient_id' => $patient->id,
            'case_id' => $patientCase->case_id,
            'assign_date' => Carbon::now(),
            'ipd_patient_department_id' => $ipdPatientDepartment->id,
            'status' => true,
            'tenant_id' => $userTenantId,
        ];

        BedAssign::create($bedAssignData);

        // opd patient create

        $opdPatientData = [
            'patient_id' => $patient->id,
            'case_id' => $patientCaseId,
            'opd_number' => OpdPatientDepartment::generateUniqueOpdNumber(),
            'height' => '5.5',
            'weight' => '65',
            'bp' => $faker->text(20),
            'appointment_date' => Carbon::now(),
            'doctor_id' => $doctorID,
            'standard_charge' => $faker->numberBetween(50, 100),
            'payment_mode' => 1,
            'symptoms' => $faker->text(30),
            'notes' => $faker->text(40),
            'is_old_patient' => false,
            'tenant_id' => $userTenantId,
        ];

        OpdPatientDepartment::create($opdPatientData);
    }
}
