<?php

namespace App\Queries;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionPlanDataTable
{
    public function get($input = []): SubscriptionPlan
    {
        /** @var SubscriptionPlan $query */
        $query = SubscriptionPlan::with('subscription')->select('subscription_plans.*');

        $query->when(isset($input['plan_type']), function (Builder $q) use ($input) {
            $q->where('frequency', '=', $input['plan_type']);
        });

        return $query;
    }
}
