<?php

namespace App\Repositories;

use App\Mail\HospitalEnquiryMail;
use App\Models\Enquiry;
use Exception;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class EnquiryRepository
 *
 * @version February 13, 2020, 8:55 am UTC
 */
class EnquiryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'full_name',
        'contact_no',
        'message',
        'viewed_by',
        'status',
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
        return Enquiry::class;
    }

    public function store(array $input): bool
    {
        try {
            $enquiry = Enquiry::create($input);
            $enquiry->getEnquiryTypeAttribute();
            $input['purpose'] = $enquiry->getEnquiryTypeAttribute();
            Mail::to(getUser()->email)
                ->send(new HospitalEnquiryMail('emails.hospital_enquiry', __('messages.new_change.enquiry_mail'), $input));
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }
}
