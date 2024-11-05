<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Traits\PopulateTenantID;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentTransaction extends Model
{
    use BelongsToTenant, PopulateTenantID;

    protected $table = 'appointment_transactions';

    public $fillable = [
        'appointment_id',
        'transaction_type',
        'transaction_id',
        'tenant_id',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
