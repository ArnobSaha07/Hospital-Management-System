<?php

namespace Database\Seeders;

use App\Models\HospitalSchedule;
use Illuminate\Database\Seeder;

class DefaultHospitalScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTenantId = session('tenant_id', null);

        for ($i = 1; $i <= 7; $i++) {
            $data = [
                'day_of_week' => $i,
                'start_time' => '08:00',
                'end_time' => '23:45',
                'tenant_id' => $userTenantId,
            ];

            HospitalSchedule::create($data);
        }
    }
}
