<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorDepartment;
use App\Models\HospitalSchedule;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultDoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $userTenantId = session('tenant_id', null);

        $departmentID = Department::whereName('Doctor')->first()->id;
        $doctorDepartmentID = DoctorDepartment::where('tenant_id', $userTenantId)->first()->id;

        $doctorData = [
            'department_id' => $departmentID,
            'first_name' => $faker->unique()->name(),
            'last_name' => $faker->unique()->name(),
            'email' => $faker->unique()->safeEmail(),
            'password' => Hash::make('123456'),
            'designation' => 'doctor',
            'qualification' => 'MBBS',
            'status' => 1,
            'email_verified_at' => Carbon::now(),
            'tenant_id' => $userTenantId,
        ];

        $user = User::create($doctorData);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'doctor_department_id' => $doctorDepartmentID,
            'specialist' => 'surgery',
            'tenant_id' => $userTenantId,
        ]);
        $ownerId = $doctor->id;
        $ownerType = Doctor::class;

        $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
        $user->assignRole($departmentID);

        // doctor schedule create

        $scheduleData = [
            'doctor_id' => $doctor->id,
            'per_patient_time' => '00:15:15',
            'tenant_id' => $userTenantId,
        ];

        $schedule = Schedule::create($scheduleData);

        $daysArr = HospitalSchedule::WEEKDAY_FULL_NAME;

        for ($i = 1; $i <= 7; $i++) {
            $data = [
                'doctor_id' => $doctor->id,
                'schedule_id' => $schedule->id,
                'available_on' => $daysArr[$i],
                'available_from' => '08:30:00',
                'available_to' => '12:30:05',
            ];

            $scheduleDay = ScheduleDay::create($data);
        }
    }
}
