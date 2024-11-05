<?php

namespace App\Repositories;

use App\Models\BedType;

/**
 * Class BedTypeRepository
 *
 * @version February 17, 2020, 8:08 am UTC
 */
class BedTypeRepository extends BaseRepository
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
        return BedType::class;
    }
}
