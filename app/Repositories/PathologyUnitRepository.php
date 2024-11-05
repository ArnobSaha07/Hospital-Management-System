<?php

namespace App\Repositories;

use App\Models\PathologyUnit;

/**
 * Class PathologyCategoryRepository
 *
 * @version April 11, 2020, 5:39 am UTC
 */
class PathologyUnitRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        return PathologyUnit::class;
    }
}
