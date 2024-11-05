<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Eloquent as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Doctor
 *
 * @version February 13, 2020, 8:55 am UTC
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 *
 * @method static Builder|Doctor newModelQuery()
 * @method static Builder|Doctor newQuery()
 * @method static Builder|Doctor query()
 * @method static Builder|Doctor whereCreatedAt($value)
 * @method static Builder|Doctor whereId($value)
 * @method static Builder|Doctor whereSpecialist($value)
 * @method static Builder|Doctor whereUpdatedAt($value)
 * @method static Builder|Doctor whereUserId($value)
 *
 * @mixin Model
 *
 * @property int $user_id
 * @property int $department_id
 * @property string $specialist
 * @property-read Address $address
 *
 * @method static Builder|Doctor whereDepartmentId($value)
 *
 * @property int $doctor_department_id
 *
 * @method static Builder|Doctor whereDoctorDepartmentId($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PatientCase[] $cases
 * @property-read int|null $cases_count
 * @property-read \App\Models\DoctorDepartment $department
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Patient[] $patients
 * @property-read int|null $patients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Appointment[] $appointments
 * @property-read int|null $appointments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Schedule[] $schedules
 * @property-read int|null $schedules_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmployeePayroll[] $payrolls
 * @property-read int|null $payrolls_count
 * @property int $is_default
 *
 * @method static Builder|Doctor whereIsDefault($value)
 */
class Doctor extends Model implements HasMedia
{
    use BelongsToTenant, PopulateTenantID, InteractsWithMedia;

    public $table = 'doctors';

    public $fillable = [
        'user_id',
        'doctor_department_id',
        'specialist',
        'description',
        'appointment_charge',
        'google_json_file_path'
    ];

    const STATUS_ALL = 2;

    const ACTIVE = 1;

    const INACTIVE = 0;

    const STATUS_ARR = [
        self::STATUS_ALL => 'All',
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Deactive',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'doctor_department_id' => 'integer',
        'specialist' => 'string',
        'description' => 'string',
        'appointment_charge' => 'double',
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
        'designation' => 'required',
        'gender' => 'required',
        'qualification' => 'required',
        'dob' => 'nullable|date',
        'specialist' => 'required',
        'image' => 'mimes:jpeg,png,jpg,gif,webp',
    ];

    public const GOOGLE_JSON_FILE_PATH = 'google_json_file';

    public function doctorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'owner');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(DoctorDepartment::class, 'doctor_department_id');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(PatientCase::class, 'doctor_id');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_cases', 'doctor_id', 'patient_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ScheduleDay::class, 'doctor_id');
    }

    public function payrolls(): MorphMany
    {
        return $this->morphMany(EmployeePayroll::class, 'owner');
    }

    public function prepareDoctorData()
    {
        return [
            'id' => $this->id,
            'title' => $this->user->full_name,
        ];
    }

    public function prepareDoctor()
    {
        return [
            'id' => $this->id,
            'doctor_name' => $this->doctorUser->full_name ?? __('messages.common.n/a'),
            'doctor_department' => $this->department->title,
            'doctor_specialist' => $this->specialist,
            'doctor_image' => $this->doctorUser->getApiImageUrlAttribute(),
        ];
    }

    public function prepareDoctorDetail()
    {
        return [
            'id' => $this->id,
            'doctor_name' => $this->doctorUser->full_name ?? __('messages.common.n/a'),
            'email' => $this->doctorUser->email ?? __('messages.common.n/a'),
            'phone' => $this->doctorUser->phone ?? __('messages.common.n/a'),
            'designation' => $this->doctorUser->designation ?? __('messages.common.n/a'),
            'doctor_department' => $this->department->title ?? __('messages.common.n/a'),
            'qualification' => $this->doctorUser->qualification ?? __('messages.common.n/a'),
            'blood_group' => $this->doctorUser->blood_group ?? __('messages.common.n/a'),
            'date_of_birth' => $this->doctorUser->dob ?? __('messages.common.n/a'),
            'gender' => $this->doctorUser->getGenderStringAttribute() ?? __('messages.common.n/a'),
            'specialist' => $this->specialist ?? __('messages.common.n/a'),
            'address1' => $this->address->address1 ?? __('messages.common.n/a'),
            'address2' => $this->address->address2 ?? __('messages.common.n/a'),
            'city' => $this->address->city ?? __('messages.common.n/a'),
            'zip' => $this->address->zip ?? __('messages.common.n/a'),
            'description' => $this->description ?? __('messages.common.n/a'),
        ];
    }
}
