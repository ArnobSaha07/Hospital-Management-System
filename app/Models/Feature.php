<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Feature
 *
 * @property int $id
 * @property string $name
 * @property int|null $submenu
 * @property array|null $route
 * @property int|null $has_parent
 * @property int|null $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Feature hasParent()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature isDefault()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereHasParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereSubmenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Feature extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    public static $rules = [
        'name' => 'required|unique:features,name',
    ];

    public $table = 'features';

    public $fillable = [
        'name',
        'submenu',
        'route',
        'has_parent',
        'is_default',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'submenu' => 'integer',
        'route' => 'array',
        'has_parent' => 'integer',
        'is_default' => 'integer',
    ];

    /**
     * @return mixed
     */
    public function scopeHasParent($query)
    {
        return $query->where('has_parent', '=', 0);
    }

    /**
     * @return mixed
     */
    public function scopeIsDefault($query)
    {
        return $query->where('is_default', '=', 0);
    }
}
