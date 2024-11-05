<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PopulateTenantID;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\CustomField
 *
 * @property int $id
 * @property string $module_name
 * @property string $field_type
 * @property string $field_name
 * @property bool $is_required
 * @property string|null $values
 * @property int $grid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereFieldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereGrid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereModuleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomField whereValues($value)
 * @mixin \Eloquent
 */
class CustomField extends Model
{
    use HasFactory, BelongsToTenant, PopulateTenantID;

    public $table = "custom_fields";

    public $fillable = [
        'module_name',
        'field_type',
        'field_name',
        'is_required',
        'values',
        'grid',
        'tenant_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'module_name' => 'string',
        'field_type' => 'string',
        'field_name' => 'string',
        'is_required' => 'boolean',
        'values' => 'string',
        'grid' => 'integer'
    ];

    public static $rules = [
        'module_name' => 'required',
        'field_type' => 'required',
        'field_name' => 'required|string',
        'grid' => 'required|numeric|min:6|max:12',
    ];

    const Appointment = 0;
    const IpdPatient = 1;
    const OpdPatient = 2;
    const Patient = 3;

    const MODULE_TYPE_ARR = [
        self::Appointment=> 'Appointment',
        self::IpdPatient => 'IPD Patient',
        self::OpdPatient => 'OPD Patient',
        self::Patient => 'Patient',
    ];

    const FIELD_TYPE_ARR = [
        0 => 'text',
        1 => 'textarea',
        2 => 'toggle',
        3 => 'number',
        4 => 'select',
        5 => 'multiSelect',
        6 => 'date',
        7 => 'date & Time',
    ];
}
