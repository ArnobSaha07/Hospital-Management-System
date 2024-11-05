<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Eloquent as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Str;

/**
 * Class Bed
 *
 * @version February 17, 2020, 10:56 am UTC
 *
 * @property int $id
 * @property int $bed_type
 * @property int $bed_id
 * @property string|null $description
 * @property float $charge
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Bed newModelQuery()
 * @method static Builder|Bed newQuery()
 * @method static Builder|Bed query()
 * @method static Builder|Bed whereBedId($value)
 * @method static Builder|Bed whereBedType($value)
 * @method static Builder|Bed whereCharge($value)
 * @method static Builder|Bed whereCreatedAt($value)
 * @method static Builder|Bed whereDescription($value)
 * @method static Builder|Bed whereId($value)
 * @method static Builder|Bed whereUpdatedAt($value)
 *
 * @mixin Model
 *
 * @property-read BedType $bedType
 * @property int $name
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bed whereName($value)
 *
 * @property int $is_available
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Bed whereIsAvailable($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BedAssign[] $bedAssigns
 * @property-read int|null $bed_assigns_count
 * @property int $is_default
 *
 * @method static Builder|Bed whereIsDefault($value)
 */
class Bed extends Model
{
    use BelongsToTenant, PopulateTenantID;

    /**
     * @var string
     */
    public $table = 'beds';

    /**
     * @var array
     */
    public $fillable = [
        'bed_type',
        'bed_id',
        'description',
        'name',
        'charge',
        'is_available',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    const NOTAVAILABLE = 0;

    const AVAILABLE = 1;

    const AVAILABLE_ALL = 2;

    const STATUS_ARR = [
        self::AVAILABLE_ALL => 'All',
        self::AVAILABLE => 'Available',
        self::NOTAVAILABLE => 'Not Available',
    ];

    const FILTER_INCOME_HEAD = [
        0 => 'All',
        1 => 'Available',
        2 => 'Not Available',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'bed_type' => 'required',
        'name' => 'required|is_unique:beds,name',
        'charge' => 'required',
    ];

    public static function generateUniqueBedId(): string
    {
        $bedId = Str::random(8);
        while (true) {
            $isExist = self::whereBedId($bedId)->exists();
            if ($isExist) {
                self::generateUniqueBedId();
            }
            break;
        }

        return $bedId;
    }

    public function bedType(): BelongsTo
    {
        return $this->belongsTo(BedType::class, 'bed_type');
    }

    public function bedAssigns(): HasMany
    {
        return $this->hasMany(BedAssign::class, 'bed_id');
    }

    public function patientAdmission(): HasMany
    {
        return $this->hasMany(PatientAdmission::class, 'bed_id');
    }

    public function patientNameRetrieved()
    {
        foreach ($this->bedAssigns as $bedAssign) {
            return $bedAssign->patient->patientUser->full_name;
        }
    }

    public function prepareData()
    {
        return [
            'bed_type' => $this->bed_type,
            'patient_name' => $this->patientNameRetrieved() ?? __('messages.common.n/a'),
        ];
    }

    public function prepareBedAssignData()
    {
        return [
            'bed_name' => $this->name,
            'patient' => $this->bedAssigns[0]->patient->patientUser->full_name ?? __('messages.common.n/a'),
            'phone' => $this->bedAssigns[0]->patient->patientUser->phone ?? __('messages.common.n/a'),
            'admission_date' => date('jS M, Y h:i A', strtotime($this->bedAssigns[0]->assign_date)) ?? __('messages.common.n/a'),
            'gender' => $this->bedAssigns[0]->patient->patientUser->gender ? 'Female' : 'Male' ?? __('messages.common.n/a'),
        ];
    }

    public function preparePatientAdmissionData($patientAdmission)
    {
        return [
            'id' => $patientAdmission->id,
            'admission_id' => $patientAdmission->patient_admission_id ?? __('messages.common.n/a'),
            'patient_name' => $patientAdmission->patient->patientUser->full_name ?? __('messages.common.n/a'),
            'patient_image' => $patientAdmission->patient->patientUser->getApiImageUrlAttribute() ?? __('messages.common.n/a'),
            'admission_date' => date('jS M, Y', strtotime($patientAdmission->admission_date)) ?? __('messages.common.n/a'),
            'admission_time' => date('h:i A', strtotime($patientAdmission->admission_date)) ?? __('messages.common.n/a'),
        ];
    }

}
