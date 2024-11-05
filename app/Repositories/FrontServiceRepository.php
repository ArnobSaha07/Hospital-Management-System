<?php

namespace App\Repositories;

use App\Models\FrontService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class FrontServiceRepository
 */
class FrontServiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'short_description',
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
        return FrontService::class;
    }

    public function store($input): bool
    {
        try {
            $frontService = FrontService::create($input);

            if (isset($input['icon']) && ! empty($input['icon'])) {
                $frontService->addMedia($input['icon'])->toMediaCollection(FrontService::PATH,
                    config('app.media_disc'));
            }

            return true;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return Builder|Builder[]|Collection|Model|void
     */
    public function updateFrontService(array $input, int $frontServiceId)
    {
        try {
            $frontService = $this->update($input, $frontServiceId);

            if (isset($input['icon']) && ! empty($input['icon'])) {
                $frontService->clearMediaCollection(FrontService::PATH);
                $frontService->addMedia($input['icon'])->toMediaCollection(FrontService::PATH,
                    config('app.media_disc'));
            }
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
