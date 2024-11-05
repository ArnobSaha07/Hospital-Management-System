<?php

namespace Database\Seeders;

use App\Models\Accountant;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAccountantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $userTenantId = session('tenant_id', null);

        $departmentID = Department::whereName('Accountant')->first()->id;

        $accountantData = [
            'department_id' => $departmentID,
            'first_name' => $faker->unique()->name(),
            'last_name' => $faker->unique()->name(),
            'email' => $faker->unique()->safeEmail(),
            'password' => Hash::make('123456'),
            'designation' => 'accountant',
            'qualification' => 'B.COM',
            'status' => 1,
            'email_verified_at' => Carbon::now(),
            'tenant_id' => $userTenantId,
        ];

        $user = User::create($accountantData);

        $accountant = Accountant::create([
            'user_id' => $user->id,
            'tenant_id' => $userTenantId,
        ]);
        $ownerId = $accountant->id;
        $ownerType = Accountant::class;

        $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
        $user->assignRole($departmentID);
    }
}
