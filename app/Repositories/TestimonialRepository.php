<?php

namespace App\Repositories;

use App\Models\Testimonial;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class TestimonialRepository
 */
class TestimonialRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
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
        return Testimonial::class;
    }

    public function store(array $input): bool
    {
        try {
            /**
             * @var Testimonial $testimonial
             */
            $testimonial = $this->create($input);
            if (! empty($input['profile'])) {
                $fileExtension = getFileName('Testimonial', $input['profile']);
                $testimonial->addMedia($input['profile'])->usingFileName($fileExtension)->toMediaCollection(Testimonial::PATH,
                    config('app.media_disc'));
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function updateTestimonial(array $input, int $testimonialId)
    {
        try {
            /**
             * @var Testimonial $testimonial
             */
            $testimonial = $this->update($input, $testimonialId);
            if (! empty($input['profile'])) {
                $testimonial->clearMediaCollection(Testimonial::PATH);
                $fileExtension = getFileName('Testimonial', $input['profile']);
                $testimonial->addMedia($input['profile'])->usingFileName($fileExtension)->toMediaCollection(Testimonial::PATH,
                    config('app.media_disc'));
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function deleteTestimonial(Testimonial $testimonial)
    {
        try {
            $testimonial->clearMediaCollection(Testimonial::PATH);
            $this->delete($testimonial->id);
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
