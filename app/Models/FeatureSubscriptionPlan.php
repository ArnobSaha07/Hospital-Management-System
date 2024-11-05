<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FeatureSubscriptionPlan
 *
 * @property int $id
 * @property int $feature_id
 * @property int|null $subscription_plan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Feature $feature
 * @property-read \App\Models\SubscriptionPlan|null $subscriptionPlan
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeatureSubscriptionPlan whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class FeatureSubscriptionPlan extends Model
{
    use HasFactory;

    public $table = 'feature_subscriptionplan';

    public $fillable = [
        'feature_id',
        'subscription_plan_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'integer',
        'feature_id' => 'integer',
        'subscription_plan_id' => 'integer',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
