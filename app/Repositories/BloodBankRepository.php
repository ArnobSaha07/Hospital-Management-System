<?php

namespace App\Repositories;

use App\Models\BloodBank;

/**
 * Class BloodBankRepository
 *
 * @version February 17, 2020, 9:23 am UTC
 */
class BloodBankRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'blood_group',
        'remained_bags',
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
        return BloodBank::class;
    }
}
