<?php

namespace App\Repositories;

use App\Models\BloodDonor;
use App\Models\BloodIssue;
use Illuminate\Support\Collection;

/**
 * Class BloodIssueRepository
 */
class BloodIssueRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'issue_date',
        'doctor_id',
        'donor_id',
        'patient_id',
        'amount',
        'remarks',
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
        return BloodIssue::class;
    }

    public function getBloodGroup($id): Collection
    {
        return BloodDonor::where('id', $id)->pluck('blood_group', 'id');
    }
}
