<?php

namespace App\Models;

use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Laravel\Sanctum\HasApiTokens;



/**
 * App\Models\User
 *
 * @property int $id
 * @property int|null $address_id
 * @property int|null $department_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|null $designation
 * @property string $phone
 * @property int $gender
 * @property string|null $education
 * @property string $dob
 * @property int $status
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read string $full_name
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 *
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAddressId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereDepartmentId($value)
 * @method static Builder|User whereDesignation($value)
 * @method static Builder|User whereDob($value)
 * @method static Builder|User whereEducation($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereGender($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhone($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property string|null $blood_group
 * @property int|null $owner_id
 * @property string|null $owner_type
 * @property-read mixed $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Department[] $roles
 * @property-read int|null $roles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBloodGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereOwnerType($value)
 *
 * @property string|null $qualification
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $language
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereQualification($value)
 *
 * @property int $is_default
 * @property string|null $stripe_id
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property string|null $trial_ends_at
 * @property-read \App\Models\Department|null $department
 * @property-read mixed $age
 * @property-read \App\Models\Patient|null $patient
 *
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static Builder|User whereCardBrand($value)
 * @method static Builder|User whereCardLastFour($value)
 * @method static Builder|User whereIsDefault($value)
 * @method static Builder|User whereStripeId($value)
 * @method static Builder|User whereTrialEndsAt($value)
 *
 * @property string|null $city
 * @property int $is_super_admin_default
 * @property int $is_admin_default
 * @property string|null $username
 * @property string $hospital_name
 * @property string|null $tenant_id
 * @property string|null $facebook_url
 * @property string|null $twitter_url
 * @property string|null $instagram_url
 * @property string|null $linkedIn_url
 * @property string $theme_mode
 * @property int|null $hospital_type_id
 * @property-read array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null $gender_string
 * @property-read \App\Models\MultiTenant|null $hospital
 * @property-read \App\Models\HospitalType|null $hospitalType
 * @property-read \App\Models\UserTenant|null $latestTenant
 * @property-read \App\Models\MultiTenant|null $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTenant[] $tenants
 * @property-read int|null $tenants_count
 *
 * @method static Builder|User whereCity($value)
 * @method static Builder|User whereFacebookUrl($value)
 * @method static Builder|User whereHospitalName($value)
 * @method static Builder|User whereHospitalTypeId($value)
 * @method static Builder|User whereInstagramUrl($value)
 * @method static Builder|User whereIsAdminDefault($value)
 * @method static Builder|User whereIsSuperAdminDefault($value)
 * @method static Builder|User whereLinkedInUrl($value)
 * @method static Builder|User whereTenantId($value)
 * @method static Builder|User whereThemeMode($value)
 * @method static Builder|User whereTwitterUrl($value)
 * @method static Builder|User whereUsername($value)
 */
class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory, BelongsToTenant, Impersonate;
    use HasApiTokens, Notifiable, InteractsWithMedia, HasRoles;

    const COLLECTION_PROFILE_PICTURES = 'profile_photo';

    const COLLECTION_MAIL_ATTACHMENTS = 'mail_attachments';

    const DARK_MODE = 1;

    const LIGHT_MODE = 0;

    const INACTIVE = 0;

    const ACTIVE = 1;

    const ALL = 2;

    const USER_ADMIN = 1;

    const STATUS_ARR = [
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Deactive',
    ];

    const FILTER_STATUS_ARR = [
        0 => 'All',
        1 => 'Active',
        2 => 'Deactive',
    ];

    const MAIN_IPD_OPD = 'IPD_OPD';

    const MAIN_BED_MGT = 'MAIN_BED_MGT';

    const MAIN_BILLING_MGT = 'MAIN_BILLING_MGT';

    const MAIN_BLOOD_BANK_MGT = 'MAIN_BLOOD_BANK_MGT';

    const MAIN_DOCUMENT = 'MAIN_DOCUMENT';

    const MAIN_DOCTOR = 'MAIN_DOCTOR';

    const MAIN_PRESCRIPTION = 'MAIN_PRESCRIPTION';

    const MAIN_DIAGNOSIS = 'MAIN_DIAGNOSIS';

    const MAIN_FINANCE = 'MAIN_FINANCE';

    const MAIN_FRONT_OFFICE = 'MAIN_FRONT_OFFICE';

    const MAIN_HOSPITAL_CHARGE = 'MAIN_HOSPITAL_CHARGE';

    const MAIN_INVENTORY = 'MAIN_INVENTORY';

    const MAIN_LIVE_CONSULATION = 'MAIN_LIVE_CONSULATION';

    const MAIN_MEDICINES = 'MAIN_MEDICINES';

    const MAIN_PATIENT_CASE = 'MAIN_PATIENT_CASE';

    const MAIN_PATHOLOGY = 'MAIN_PATHOLOGY';

    const MAIN_REPORT = 'MAIN_REPORT';

    const MAIN_RADIOLOGY = 'MAIN_RADIOLOGY';

    const MAIN_SERVICE = 'MAIN_SERVICE';

    const MAIN_SMS_MAIL = 'MAIN_SMS_MAIL';

    const MAIN_DOCTOR_BED_MGT = 'MAIN_DOCTOR_BED_MGT';

    const MAIN_DOCTOR_PATIENT_CASE = 'MAIN_DOCTOR_PATIENT_CASE';

    const MAIN_CASE_MANGER_PATIENT_CASE = 'MAIN_CASE_MANGER_PATIENT_CASE';

    const MAIN_CASE_MANGER_SERVICE = 'MAIN_CASE_MANGER_SERVICE';

    const MAIN_ACCOUNT_MANAGER_MGT = 'MAIN_ACCOUNT_MANAGER_MGT';

    const MAIN_VACCINATION_MGT = 'MAIN_VACCINATION_MGT';

    const LANGUAGES = [
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'en' => 'English',
        'fr' => 'French',
        'de' => 'German',
        'pt' => 'Portuguese',
        'ru' => 'Russian',
        'es' => 'Spanish',
        'tr' => 'Turkish',
    ];

    const LANGUAGES_IMAGE = [
        'ar' => 'assets/img/iraq.svg',
        'zh' => 'assets/img/china.svg',
        'en' => 'assets/img/united-states.svg',
        'fr' => 'assets/img/france.svg',
        'de' => 'assets/img/germany.svg',
        'pt' => 'assets/img/portugal.svg',
        'ru' => 'assets/img/russia.svg',
        'es' => 'assets/img/spain.svg',
        'tr' => 'assets/img/turkey.svg',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'address_id',
        'department_id',
        'hospital_type_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'designation',
        'phone',
        'gender',
        'qualification',
        'dob',
        'blood_group',
        'status',
        'language',
        'owner_id',
        'owner_type',
        'email_verified_at',
        'updated_at',
        'username',
        'city',
        'hospital_name',
        'tenant_id',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedIn_url',
        'is_super_admin_default',
        'is_admin_default',
        'region_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','theme_mode', 'email_verified_at'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email:filter|unique:users,email',
        'password' => 'nullable|same:password_confirmation|min:6',
        'department_id' => 'required',
        'gender' => 'required',
        'dob' => 'nullable|date',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $hospitalRules = [
        'hospital_name' => 'required|string|max:255|unique:tenants,hospital_name',
        'email' => 'required|email:filter|unique:users,email',
        'username' => 'required|string|max:12|unique:users,username',
        'password' => 'nullable|confirmed|min:6',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const IMG_COLUMN = 'image_url';

    /**
     * @var array
     */
    protected $appends = ['full_name', 'age'];

    /**
     * @var array
     */
    public static $messages = [
        'phone.digits' => 'The phone number must be 10 digits long.',
        'email.regex' => 'Please enter valid email.',
        'photo.mimes' => 'The profile image must be a file of type: jpeg, jpg, png , webp.',
    ];

    public function getFullNameAttribute(): string
    {
        return ucfirst($this->first_name).' '.ucfirst($this->last_name);
    }

    /**
     * @return mixed
     */
    public function getImageUrlAttribute()
    {
        /** @var Media $media */
        $media = $this->getMedia(self::COLLECTION_PROFILE_PICTURES)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return getUserImageInitial($this->id, $this->first_name);
    }

    /**
     * Accessor for Age.
     */
    public function getAgeAttribute()
    {
        return Carbon::parse($this->attributes['dob'])->age;
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public static function getOwnerType($ownerType): string
    {
        switch ($ownerType) {
            case Accountant::class:
                return Notification::ACCOUNTANT;
            case Patient::class:
                return Notification::PATIENT;
            case Doctor::class:
                return Notification::DOCTOR;
            case Receptionist::class:
                return Notification::RECEPTIONIST;
            case CaseHandler::class:
                return Notification::CASE_HANDLER;
            case LabTechnician::class:
                return Notification::LAB_TECHNICIAN;
            case Nurse::class:
                return Notification::NURSE;
            case Pharmacist::class:
                return Notification::PHARMACIST;
            default:
                return Notification::ADMIN;
        }
    }

    public function prepareData()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name ?? __('messages.common.n/a'),
            'last_name' => $this->last_name ?? __('messages.common.n/a'),
            'email' => $this->email ?? __('messages.common.n/a'),
            'phone_number' => $this->phone ?? __('messages.common.n/a'),
            'region_code' =>isset($this->region_code)?  $this->region_code : "+91",
            'image_url' => $this->getApiImageUrlAttribute() ?? __('messages.common.n/a'),
        ];
    }

    public function PreperHospitalData(){
        return [
            'id' => $this->id,
            'hospital_name' => $this->hospital_name ?? __('messages.common.n/a'),
            'email' => $this->email ?? __('messages.common.n/a'),
            'hospital_slug' => $this->username ?? __('messages.common.n/a'),
            'hospital_type' => $this->hospitalType->name ?? __('messages.common.n/a'),
            'hospital_type_id' => $this->hospital_type_id ??  __('messages.common.n/a'),
            'city' => $this->city ?? __('messages.common.n/a'),
            'status' => $this->status ?? __('messages.common.n/a'),
            'phone_no' => $this->phone ?? __('messages.common.n/a'),
            'region_code' =>isset($this->region_code)?  $this->region_code : "+91",
            'image_url' => $this->getApiImageUrlAttribute() ?? __('messages.common.n/a'),
        ];
    }

    public function getApiImageUrlAttribute()
    {
        $media = $this->getMedia(self::COLLECTION_PROFILE_PICTURES)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return getApiUserImageInitial($this->id, $this->first_name);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function hospitalType(): BelongsTo
    {
        return $this->belongsTo(HospitalType::class, 'hospital_type_id');
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * @return array|Application|Translator|string|null
     */
    public function getGenderStringAttribute()
    {
        if ($this->gender == 0) {
            return __('messages.user.male');
        } else {
            return __('messages.user.female');
        }
    }

    public function tenants()
    {
        return $this->hasMany(UserTenant::class);
    }

    public function latestTenant()
    {
        return $this->hasOne(UserTenant::class)->ofMany([
        ], function ($query) {
            $query->orderByDesc('last_login_at');
        });
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(MultiTenant::class, 'tenant_id', 'id');
    }

    public function gCredentials(): HasOne
    {
        return $this->hasOne(GoogleCalendarIntegration::class, 'user_id');
    }
}
