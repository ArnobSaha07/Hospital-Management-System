<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DoctorDepartment;
use App\Models\MultiTenant;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserTenant;
use Carbon\Carbon;
use DB;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DefaultHospitalTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $faker = Factory::create();

            for ($i = 0; $i < 2; $i++) {
                $adminRole = Department::whereName('Admin')->first();

                $hospital_name = $faker->unique()->name();
                $slug = $faker->unique()->slug(1);

                $data = [
                    'hospital_name' => $hospital_name,
                    'username' => $slug,
                    'email' => $faker->unique()->safeEmail(),
                    'password' => Hash::make('123456'),
                    'status' => User::ACTIVE,
                    'first_name' => $hospital_name,
                    'phone' => '+918585858585',
                    'department_id' => $adminRole->id,
                    'is_admin_default' => 1,
                    'email_verified_at' => Carbon::now(),
                ];

                $user = User::create($data);

                $user->assignRole($adminRole);

                $tenant = MultiTenant::create([
                    'tenant_username' => $slug,
                    'hospital_name' => $hospital_name,
                ]);

                $user->update(['tenant_id' => $tenant->id]);

                UserTenant::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'last_login_at' => Carbon::now(),
                ]);

                $doctorDep = new DoctorDepartment();
                $doctorDep->tenant_id = $tenant->id;
                $doctorDep->title = 'Doctor';
                $doctorDep->saveQuietly();

                // creating settings and assigning the modules to the created user.
                session(['tenant_id' => $tenant->id]);

                Artisan::call('db:seed', ['--class' => 'SettingsTableSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'AddSocialSettingTableSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'DefaultModuleSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'FrontSettingHomeTableSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'FrontSettingTableSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'AddAppointmentFrontSettingTableSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'AddHomePageBoxContentSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'AddDoctorFrontSettingTableSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'FrontServiceSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'GoogleRecaptchaSettingSeeder', '--force' => true]);

                Artisan::call('db:seed', ['--class' => 'DefaultHospitalCurrenciesSeeder', '--force' => true]);
                Artisan::call('db:seed', ['--class' => 'DefaultHospitalScheduleSeeder', '--force' => true]);

                Artisan::call('db:seed', ['--class' => 'DefaultBloodBankSeeder', '--force' => true]);

                // Default users create

                for ($j = 0; $j < 5; $j++) {
                    Artisan::call('db:seed', ['--class' => 'DefaultAccountantSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultDoctorSeeder', '--force' => true]);

                    Artisan::call('db:seed', ['--class' => 'DefaultBloodDonorSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultBloodDonationSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultBedTypeAndBedSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultPatientSeeder', '--force' => true]);

                    Artisan::call('db:seed', ['--class' => 'DefaultNurseSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultReceptionistSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultPharmacistSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultCaseManagerSeeder', '--force' => true]);
                    Artisan::call('db:seed', ['--class' => 'DefaultLabTechnicianSeeder', '--force' => true]);
                }

                // assign the default plan to the user when they registers.
                $subscriptionPlan = SubscriptionPlan::where('is_default', 1)->first();
                $trialDays = $subscriptionPlan->trial_days;
                $subscription = [
                    'user_id' => $user->id,
                    'subscription_plan_id' => $subscriptionPlan->id,
                    'plan_amount' => $subscriptionPlan->price,
                    'plan_frequency' => $subscriptionPlan->frequency,
                    'starts_at' => Carbon::now(),
                    'ends_at' => Carbon::now()->addDays($trialDays),
                    'trial_ends_at' => Carbon::now()->addDays($trialDays),
                    'status' => Subscription::ACTIVE,
                    'sms_limit' => $subscriptionPlan->sms_limit,
                ];

                Subscription::create($subscription);
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
