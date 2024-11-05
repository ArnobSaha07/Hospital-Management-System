<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Support\Collection;

/**
 * Class ItemRepository
 *
 * @version August 26, 2020, 10:11 am UTC
 */
class ItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'item_category_id',
        'unit',
        'description',
        'avaiable_quantity',
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
        return Item::class;
    }

    public function getItemCategories(): Collection
    {
        return ItemCategory::all()->pluck('name', 'id')->sort();
    }
}
