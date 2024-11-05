<?php

namespace App\Models;

use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\PurchasedMedicine
 *
 * @property int $id
 * @property int $purchase_medicines_id
 * @property int|null $medicine_id
 * @property string|null $expiry_date
 * @property string $lot_no
 * @property float $tax
 * @property int $quantity
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $tenant_id
 * @property-read \App\Models\Medicine|null $medicines
 * @property-read \App\Models\MultiTenant $tenant
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereLotNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereMedicineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine wherePurchaseMedicinesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedMedicine whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PurchasedMedicine extends Model
{
    use BelongsToTenant, PopulateTenantID;

    protected $fillable =
        [
            'purchase_medicines_id',
            'medicine_id',
            'lot_no',
            'expiry_date',
            'quantity',
            'amount',
            'tax',
            'tenant_id',
        ];

    public function medicines(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }
}
