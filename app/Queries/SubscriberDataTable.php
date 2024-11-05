<?php

namespace App\Queries;

use App\Models\Subscribe;

/**
 * Class SubscriberDataTable
 */
class SubscriberDataTable
{
    public function get(): Subscribe
    {
        /** @var Subscribe $query */
        $query = Subscribe::select('subscribes.*');

        return $query;
    }
}
