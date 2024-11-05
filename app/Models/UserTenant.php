<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserTenant
 *
 * @property int $id
 * @property int $user_id
 * @property string $tenant_id
 * @property string $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTenant whereUserId($value)
 *
 * @mixin \Eloquent
 */
class UserTenant extends Model
{
    protected $table = 'user_tenants';

    protected $fillable = [
        'user_id',
        'tenant_id',
        'last_login_at',
    ];
}
