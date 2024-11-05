<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\BedType;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DefaultBedTypeAndBedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $userTenantId = session('tenant_id', null);

        $bedTypeData = [
            'title' => $faker->word(),
            'description' => $faker->sentence(),
            'tenant_id' => $userTenantId,
        ];

        $bedType = BedType::create($bedTypeData);

        $bedData = [
            'bed_type' => $bedType->id,
            'bed_id' => mb_strtoupper(Bed::generateUniqueBedId()),
            'name' => $faker->word(),
            'description' => $faker->sentence(),
            'charge' => $faker->numberBetween(200, 500),
            'tenant_id' => $userTenantId,
        ];

        Bed::create($bedData);
    }
}
