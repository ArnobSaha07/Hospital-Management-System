<?php

namespace App\Repositories;

use App\Models\DoctorDepartment;

/**
 * Class DoctorDepartmentRepository
 *
 * @version February 21, 2020, 5:23 am UTC
 */
class DoctorDepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'description',
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
        return DoctorDepartment::class;
    }
}
