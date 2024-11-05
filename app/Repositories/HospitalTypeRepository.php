<?php

namespace App\Repositories;

use App\Models\HospitalType;

/**
 * Class HospitalTypeRepository
 *
 * @version September 5, 2022, 8:14 pm UTC
 */
class HospitalTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
    ];

    /**
     * Return searchable fields
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HospitalType::class;
    }
}
