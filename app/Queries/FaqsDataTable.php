<?php

namespace App\Queries;

use App\Models\Faqs;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class FaqsDataTable
 */
class FaqsDataTable
{
    public function get(): Builder
    {
        return Faqs::query()->select('faqs.*');
    }
}
