<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\IpdPayment
 *
 * @property int $id
 * @property int $ipd_patient_department_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon $date
 * @property int $payment_mode
 * @property int|null $transaction_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $ipd_payment_document_url
 * @property-read mixed $payment_mode_name
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereIpdPatientDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment wherePaymentMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpdPayment whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class IpdPayment extends Model implements HasMedia
{
    use BelongsToTenant, PopulateTenantID, InteractsWithMedia;

    public const IPD_PAYMENT_PATH = 'ipd_payments';

    public $table = 'ipd_payments';

    public const PAYMENT_MODES_STRIPE = 3;

    public const PAYMENT_MODES_PAYPAL = 4;

    public const PAYMENT_MODES_RAZORPAY = 5;

    // public const PAYMENT_MODES_PAYTM = 6;

    public const PAYMENT_MODES_PAYSTACK = 7;

    public const PAYMENT_MODES_PHONEPE = 8;

    public const PAYMENT_MODES_FLUTTERWAVE = 9;

    const PAYMENT_MODES = [
        1 => 'Cash',
        2 => 'Cheque',
        3 => 'Stripe',
        4 => 'Paypal',
        5 => 'Razorpay',
        // 6 => 'Paytm',
        7 => 'PayStack',
        8 => 'PhonePe',
        9 => 'FlutterWave',
    ];
    const NEW_PAYMENT_MODES = [
        1 => 'Cash',
        2 => 'Cheque',
        3 => 'Stripe',
        4 => 'Paypal',
        5 => 'Razorpay',
        7 => 'PayStack',
        8 => 'PhonePe',
        9 => 'FlutterWave',
    ];

    public $fillable = [
        'ipd_patient_department_id',
        'payment_mode',
        'date',
        'notes',
        'amount',
        'transaction_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'ipd_patient_department_id' => 'integer',
        'payment_mode' => 'integer',
        'date' => 'date',
        'amount' => 'double',
        'transaction_id' => 'integer',
        'notes' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'payment_mode' => 'required',
        'date' => 'required|date',
        'amount' => 'required',
        'notes' => 'nullable',
        'file' => 'mimes:jpeg,png,jpg,gif,webp',
    ];

    /**
     * @var array
     */
    protected $appends = ['ipd_payment_document_url', 'payment_mode_name'];

    /**
     * @return mixed
     */
    public function getIpdPaymentDocumentUrlAttribute()
    {
        /** @var Media $media */
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getPaymentModeNameAttribute()
    {
        return self::PAYMENT_MODES[$this->payment_mode];
    }
}
