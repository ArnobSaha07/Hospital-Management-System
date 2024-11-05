<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Traits\PopulateTenantID;

/**
 * App\Models\PathologyUnit
 *
 * @property int $id
 * @property string $name
 * @property string|null $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MultiTenant|null $tenant
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PathologyUnit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PathologyUnit extends Model
{
    use BelongsToTenant, PopulateTenantID;

    public $table = 'pathology_units';

    public $fillable = [
        'name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|is_unique:pathology_units,name',
    ];
}
