<?php

namespace App\Repositories;

use App\Models\ServiceSlider;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

/**
 * Class ServiceSliderRepository
 */
class ServiceSliderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
    ];

    /**
     * @return array|string[]
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return ServiceSlider::class;
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store($input)
    {
        $serviceSlider = ServiceSlider::create($input);

        if (isset($input['img_url']) && ! empty($input['img_url'])) {
            $media = $serviceSlider->addMedia($input['img_url'])->toMediaCollection(ServiceSlider::SERVICE_SLIDER,
                config('app.media_disc'));
            $serviceSlider->update(['img_url' => $media->getUrl()]);
        }

        return $serviceSlider;
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update($input, $id)
    {
        $serviceSlider = ServiceSlider::findOrFail($id);
        $serviceSlider->update($input);

        if (isset($input['img_url']) && ! empty($input['img_url'])) {
            $serviceSlider->clearMediaCollection(ServiceSlider::SERVICE_SLIDER);
            $serviceSlider->media()->delete();
            $serviceSlider->addMedia($input['img_url'])->toMediaCollection(ServiceSlider::SERVICE_SLIDER,
                config('app.media_disc'));
        }

        return $serviceSlider;
    }
}
