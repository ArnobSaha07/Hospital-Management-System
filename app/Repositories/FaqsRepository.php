<?php

namespace App\Repositories;

use App\Models\Faqs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FaqsRepository
 */
class FaqsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'question',
        'answer',
    ];

    /**
     * @return array|string[]
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Faqs::class;
    }

    /**
     * @return mixed
     */
    public function store($input)
    {
        $faqs = Faqs::create($input);

        return $faqs;
    }

    /**
     * @return Builder|Builder[]|Collection|Model
     */
    public function updateFaqs(array $input, $faqs)
    {
        $faqs->update($input);

        return $faqs;
    }
}
