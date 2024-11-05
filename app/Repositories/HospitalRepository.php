<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorDepartment;
use App\Models\HospitalType;
use App\Models\MultiTenant;
use App\Models\Patient;
use App\Models\PatientCase;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserTenant;
use Carbon\Carbon;
use Exception;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class UserRepository
 *
 * @version January 11, 2020, 11:09 am UTC
 */
class HospitalRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'phone',
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
        return User::class;
    }

    /**
     * @throws \Throwable
     */
    public function store($input): bool
    {
        try {

            DB::beginTransaction();

            $input['password'] = Hash::make($input['password']);
            $input['status'] = User::ACTIVE;
            $input['first_name'] = $input['hospital_name'];

            $adminRole = Department::whereName('Admin')->first();
            // $input['phone'] = preparePhoneNumber($input, 'phone');
            $input['department_id'] = $adminRole->id;
            if(!empty(getSuperAdminSettingValue()['default_language']->value)){
                $input['language'] = getSuperAdminSettingValue()['default_language']->value;
            }
            $user = User::create($input);
            $user->sendEmailVerificationNotification();
            $user->assignRole($adminRole);

            $tenant = MultiTenant::create([
                'tenant_username' => $input['username'], 'hospital_name' => $input['hospital_name'],
            ]);

            $user->update([
                'tenant_id' => $tenant->id,
            ]);

            UserTenant::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'last_login_at' => Carbon::now(),
            ]);

            $doctorDep = new DoctorDepartment();
            $doctorDep->tenant_id = $tenant->id;
            $doctorDep->title = 'Doctor';
            $doctorDep->saveQuietly();

            /*
            $subscription = [
                'user_id'    => $user->id,
                'start_date' => Carbon::now(),
                'end_date'   => Carbon::now()->addDays(6),
                'status'     => 1,
            ];
            Subscription::create($subscription);
            */

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

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }

    public function updateHospital($input, $user): bool
    {
        try {

            DB::beginTransaction();

            // $input['phone'] = preparePhoneNumber($input, 'phone');
            $input['first_name'] = $input['hospital_name'];

            $user->update(Arr::except($input, ['username']));
            $userTenant = MultiTenant::find($user->tenant_id);
            $userTenant->hospital_name = $input['hospital_name'];
            $userTenant->save();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function deleteHospital($id): bool
    {
        try {
            /**
             * @var User $user
             */
            $user = User::findOrFail($id);
            $tenant = MultiTenant::where('id', $user->tenant_id);
            Doctor::whereNotNull('id')->where('tenant_id', $user->tenant_id)->delete();
            $tenant->delete();
            if ($tenant) {
                $user->clearMediaCollection(User::COLLECTION_PROFILE_PICTURES);
                $user->delete();
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return Builder[]|null
     */
    public function getUserData($id): ?array
    {
        $data['hospital'] = User::with(['hospital'])->findOrFail($id)->append(['gender_string', 'image_url']);
        $data['hospitalUser'] = User::where('tenant_id', $data['hospital']->tenant_id)->where('id',
            '!=', $id)->get();
        $data['statusArr'] = User::STATUS_ARR;
        $userRole = ['Admin', 'Super Admin'];
        $data['roles'] = Department::whereNotIn('name', $userRole)->orderBy('name')->pluck('name', 'id')->toArray();
        $data['paymentType'] = Transaction::PAYMENT_TYPES;
        $data['caseCount'] = PatientCase::where('tenant_id', $data['hospital']->tenant_id)->count();
        $data['patientCount'] = Patient::where('tenant_id', $data['hospital']->tenant_id)->count();
        $data['appointmentCount'] = Appointment::where('tenant_id', $data['hospital']->tenant_id)->count();
        // dd($data);
        return $data;
    }

    public function getSyncList()
    {
        $data['hospitalType'] = HospitalType::select(['name', 'id'])->toBase()->pluck('name', 'id')->toArray();

        return $data;
    }
}
