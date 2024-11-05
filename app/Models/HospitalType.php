<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class HospitalType
 *
 * @version September 5, 2022, 8:14 pm UTC
 *
 * @property string $type
 */
class HospitalType extends Model
{
    //    use SoftDeletes;

    use HasFactory;

    public $table = 'hospital_type';

    //    protected $dates = ['deleted_at'];
    protected $hidden = ['created_at', 'updated_at'];

    public $fillable = [
        'id',
        'name',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:hospital_type,name',
    ];

    public function prepareHospitalType(){
        return [
            'id' => $this->id ?? __('messages.common.n/a'),
            'name' => $this->name ??  __('messages.common.n/a')
        ];
    }
}
