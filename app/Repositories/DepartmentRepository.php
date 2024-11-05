<?php

namespace App\Repositories;

use App\Models\Department;

/**
 * Class DepartmentRepository
 *
 * @version February 12, 2020, 5:39 am UTC
 */
class DepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'is_active',
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
        return Department::class;
    }
}
