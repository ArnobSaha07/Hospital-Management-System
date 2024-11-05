<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\FrontService
 *
 * @property int $id
 * @property string $name
 * @property string $short_description
 * @property string|null $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $icon_url
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\MultiTenant|null $tenant
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService query()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontService whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FrontService extends Model implements HasMedia
{
    use BelongsToTenant, HasFactory, InteractsWithMedia;

    public $table = 'front_services';

    public const PATH = 'front-services';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'short_description' => 'required',
        'icon' => 'mimes:jpeg,png,jpg,gif,webp,svg',
    ];

    protected $fillable = [
        'name',
        'short_description',
    ];

    protected $appends = ['icon_url'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'short_description' => 'string',
    ];

    /**
     * @return mixed
     */
    public function getIconUrlAttribute()
    {
        /** @var Media $media */
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return $this->value;
    }
}
