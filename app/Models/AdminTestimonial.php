<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\AdminTestimonial
 *
 * @property int $id
 * @property string $name
 * @property string $position
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $image_url
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminTestimonial whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AdminTestimonial extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const PATH = 'testimonials';

    /**
     * @var string
     */
    public $table = 'admin_testimonials';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'position',
        'description',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['image_url'];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'position' => 'string',
        'description' => 'string',
    ];

    /**
     * @var string[]
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'position' => 'required',
        'description' => 'required|max:295',
        'profile' => 'mimes:jpeg,png,jpg',
    ];

    public function getImageUrlAttribute()
    {
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('landing_front/images/thomas-james.png');
    }
}
