<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\SmartPatientCard
 *
 * @property int $id
 * @property string $template_name
 * @property string $header_color
 * @property bool $show_email
 * @property bool $show_phone
 * @property bool $show_dob
 * @property bool $show_blood_group
 * @property bool $show_address
 * @property bool $show_patient_unique_id
 * @property string|null $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MultiTenant|null $tenant
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereHeaderColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereShowAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereShowBloodGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereShowDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereShowEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereShowPatientUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereShowPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereTemplateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmartPatientCard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SmartPatientCard extends Model
{
    use HasFactory, BelongsToTenant, PopulateTenantID;

    public $table = 'smart_patient_cards';

    public $fillable = [
        'template_name',
        'header_color',
        'show_email',
        'show_phone',
        'show_dob',
        'show_blood_group',
        'show_address',
        'show_patient_unique_id',
        'tenant_id',
    ];

        /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'template_name' => 'string',
        'header_color' => 'string',
        'show_email' => 'boolean',
        'show_phone' => 'boolean',
        'show_dob' => 'boolean',
        'show_blood_group' => 'boolean',
        'show_address' => 'boolean',
        'show_patient_unique_id' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'template_name' => 'required',
        'header_color' => 'required',
    ];
}
