<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int|null $subscription_plan_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SubscriptionPlan|null $SubscriptionPlan
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereCurrentEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereCurrentStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subscription whereUserId($value)
 */
class Subscription extends Model
{
    use HasFactory;

    const ACTIVE = 1;

    const INACTIVE = 0;

    const TYPE_FREE = 0;

    const TYPE_STRIPE = 1;

    const TYPE_PAYPAL = 2;

    const TYPE_RAZORPAY = 3;

    const TYPE_CASH = 4;

    const TYPE_PAYTM = 5;

    const TYPE_PAYSTACK = 6;

    const EXPIRED = 0;

    const NOT_EXPIRED = 1;

    const PAYMENT_TYPES = [
        self::TYPE_FREE => 'Free Plan',
        self::TYPE_STRIPE => 'Stripe',
        self::TYPE_PAYPAL => 'PayPal',
        self::TYPE_RAZORPAY => 'Razorpay',
        self::TYPE_CASH => 'Manual',
        // self::TYPE_PAYTM => 'Paytm',
        self::TYPE_PAYSTACK => 'Paystack',
    ];

    const STATUS_ARR = [
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Deactive',
    ];

    const PLAN_EXPIRE_ARR = [
        self::EXPIRED => 'Expired',
        self::NOT_EXPIRED => 'Not Expired',
    ];

    const MONTH = 'Month';

    const YEAR = 'Year';

    public $fillable = [
        'user_id', 'subscription_plan_id', 'transaction_id', 'plan_amount', 'plan_frequency', 'starts_at', 'ends_at',
        'trial_ends_at', 'sms_limit', 'status',
    ];

    /**
     * @var string
     */
    protected $table = 'subscriptions';

    protected $hidden = ['created_at', 'updated_at'];


    /**
     * @var string[]
     */
    protected $casts = [
        'user_id' => 'integer',
        'subscription_plan_id' => 'integer',
        'transaction_id' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions(): HasOne
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }

    public function isExpired()
    {
        $now = Carbon::now();

        // this means the subscription is ended.
        if ((! empty($this->trial_ends_at) && $this->trial_ends_at < $now) || $this->ends_at < $now) {
            return true;
        }

        // this means the subscription is not ended.
        return false;
    }

    public function preperSubscription(){
        return [
            'id' => $this->id,
            'hospital_name' => $this->user->full_name ?? __('messages.common.n/a'),
            'subscription_plan_name' => $this->subscriptionPlan->name ?? __('messages.common.n/a'),
            'amount' => $this->subscriptionPlan->price ?? __('messages.common.n/a'),
            'currency' => getAdminCurrencySymbol($this->subscriptionPlan->currency) ?? __('messages.common.n/a'),
            'plan_frequency' => $this->plan_frequency == 1 ? 'Month' : 'Year',
            'start_date' => \Carbon\Carbon::parse($this->starts_at)->translatedFormat('jS M,Y')?? __('messages.common.n/a'),
            'start_time' => \Carbon\Carbon::parse($this->starts_at)->translatedFormat('h:i A') ?? __('messages.common.n/a'),
            'expire_date' => \Carbon\Carbon::parse($this->ends_at)->translatedFormat('jS M,Y')?? __('messages.common.n/a'),
            'expire_time' => \Carbon\Carbon::parse($this->ends_at)->translatedFormat('h:i A') ?? __('messages.common.n/a'),
            'sms_limit' => $this->sms_limit ?? __('messages.common.n/a'),
            'status' => $this->status == 1 ? 'Active' : 'Deactive'
        ];
    }
}
