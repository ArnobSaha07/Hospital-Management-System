<?php

namespace App\Repositories;

use App\Models\BloodDonation;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class BloodDonationRepository
 */
class BloodDonationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'blood_donor_id',
        'donation_date',
        'bags',
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
        return BloodDonation::class;
    }

    public function createBloodDonation(array $input)
    {
        try {
            /** @var BloodDonation $bloodDonation */
            $bloodDonation = $this->create($input);

            /** @var BloodDonation $bloodDonation */
            $bloodDonation = BloodDonation::with('bloodDonor.bloodBank')->find($bloodDonation->id);
            $bloodBank = $bloodDonation->bloodDonor->bloodBank;
            $remainedBags = $bloodBank->remained_bags + $input['bags'];
            $bloodBank->update(['remained_bags' => $remainedBags]);

            $bloodDonation->bloodDonor->update(['last_donate_date' => $bloodDonation->created_at]);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function updateBloodDonation(array $input, BloodDonation $bloodDonation)
    {
        try {
            /** @var BloodDonation $bloodDonation */
            $bloodDonation = BloodDonation::with('bloodDonor.bloodBank')->find($bloodDonation->id);
            $currentBags = $bloodDonation->bags;

            /** @var BloodDonation $bloodDonation */
            $bloodDonation = $this->update($input, $bloodDonation->id);

            $bloodBank = $bloodDonation->bloodDonor->bloodBank;
            $remainedBags = ($bloodBank->remained_bags - $currentBags) + $input['bags'];

            $bloodBank->update(['remained_bags' => $remainedBags]);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
