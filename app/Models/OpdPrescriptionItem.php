<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\OpdPrescriptionItem
 *
 * @property int $id
 * @property int $opd_prescription_id
 * @property int $category_id
 * @property int $medicine_id
 * @property string $dosage
 * @property int $dose_interval
 * @property int|null $day
 * @property int|null $time
 * @property string $instruction
 * @property string|null $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereOpdPrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereMedicineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereDosage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereDoseInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereInstruction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OpdPrescriptionItem whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 * @property-read Category $medicineCategory
 * @property-read Medicine $medicine
 *
 */
class OpdPrescriptionItem extends Model
{
    use BelongsToTenant, PopulateTenantID;

    public $table = 'opd_prescription_items';

    public $fillable = [
        'opd_prescription_id',
        'category_id',
        'medicine_id',
        'dosage',
        'instruction',
        'dose_interval',
        'day',
        'time',
    ];

    /**
    * The attributes that should be casted to native types.
    * @var array
    */

    protected $casts = [
        'id' => 'integer',
        'opd_prescription_id' => 'integer',
        'category_id' => 'integer',
        'medicine_id' => 'integer',
        'dosage' => 'string',
        'instruction' => 'string',
        'dose_interval' => 'integer',
        'day' => 'integer',
        'time' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */

    public static $rules = [
        'category_id' => 'required',
    ];


    public function medicineCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }


    public function prescriptionMedicines(): BelongsTo
    {
        return $this->belongsTo(PrescriptionMedicineModal::class, 'opd_prescription_id');
    }


}
