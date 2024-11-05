<?php

namespace App\Repositories;

use App\Models\PathologyParameter;
use App\Models\PathologyUnit;

/**
 * Class PathologyCategoryRepository
 *
 * @version April 11, 2020, 5:39 am UTC
 */
class PathologyParameterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'parameter_name',
        'reference_range',
        'unit_id',
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
        return PathologyParameter::class;
    }

    /**
     * @return mixed
     */
    public function getPathologyUnitData()
    {
        $data = PathologyUnit::all()->pluck('name','id');

        return $data;
    }
}
