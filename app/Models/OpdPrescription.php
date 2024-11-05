<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\OpdPrescription
 *
 * @property int $id
 * @property int $opd_patient_department_id
 * @property int $category_id
 * @property int $medicine_id
 * @property string $dosage
 * @property int $dose_interval
 * @property int|null $day
 * @property int|null $time
 * @property string $instruction
 * @property string|null $header_note
 * @property string|null $footer_note
 * @property string|null $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|OpdPrescriptionItem[] $opdPrescriptionItems
 * @property-read int|null $opd_prescription_items_count
 * @property-read \App\Models\OpdPatientDepartment $patient
 * @property-read \App\Models\MultiTenant|null $tenant
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription query()
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription whereOpdPatientDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription whereHeaderNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription whereFooterNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescription whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class OpdPrescription extends Model
{
    use BelongsToTenant, PopulateTenantID;

    public $table = 'opd_prescriptions';

    public $fillable = [
        'opd_patient_department_id',
        'header_note',
        'footer_note',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'opd_patient_department_id' => 'integer',
        'header_note' => 'string',
        'footer_note' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'category_id.*' => 'required',
        'dosage.*' => 'required',
        'day.*' => 'required',
        'dose_interval.*' => 'required',
        'time.*' => 'required',
        'instruction.*' => 'required',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(OpdPatientDepartment::class, 'opd_patient_department_id');
    }

    public function opdPrescriptionItems(): HasMany
    {
        return $this->hasMany(OpdPrescriptionItem::class, 'opd_prescription_id');
    }

}
