<?php

namespace Database\Seeders;

use App\Models\BloodDonation;
use App\Models\BloodDonor;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DefaultBloodDonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $userTenantId = session('tenant_id', null);
        $bloodDonorId = BloodDonor::where('tenant_id', $userTenantId)->inRandomOrder()->first()->id;

        $bloodDonationData = [
            'blood_donor_id' => $bloodDonorId,
            'bags' => $faker->numberBetween(1, 100),
            'tenant_id' => $userTenantId,
        ];

        $bloodDonation = BloodDonation::create($bloodDonationData);
    }
}
