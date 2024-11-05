<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Models\Feature;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AppBaseController;
use App\Repositories\SubscriptionPlanRepository;

class SuperAdminSubscriptionsAPIController extends AppBaseController
{
    private $subscriptionPlanRepository;

    public function __construct(SubscriptionPlanRepository $subscriptionPlanRepo)
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepo;
    }

    public function showSubscription(Request $request)
    {
        $input = $request->all();
        $input['skip'] = $input['skip'] ?? 0;
        $subscriptions = Subscription::with(['subscriptionPlan', 'user'])->skip($input['skip'])->take(50)->orderBy('id', 'desc')->get();

        $data = [];

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            // $data[] = $subscription->preperSubscription();
            $data[] =[
                    'id' => $subscription->id,
                    'hospital_name' => $subscription->user->full_name ?? __('messages.common.n/a'),
                    'subscription_plan_name' => $subscription->subscriptionPlan->name ?? __('messages.common.n/a'),
                    'amount' => $subscription->subscriptionPlan->price ?? __('messages.common.n/a'),
                    'currency' => getAdminCurrencySymbol($subscription->subscriptionPlan->currency) ?? __('messages.common.n/a'),
                    'plan_frequency' => $subscription->plan_frequency == 1 ? 'Month' : 'Year',
                    'start_date' => \Carbon\Carbon::parse($subscription->starts_at)->translatedFormat('jS M,Y')?? __('messages.common.n/a'),
                    'start_time' => \Carbon\Carbon::parse($subscription->starts_at)->translatedFormat('h:i A') ?? __('messages.common.n/a'),
                    'expire_date' => \Carbon\Carbon::parse($subscription->ends_at)->translatedFormat('jS M,Y')?? __('messages.common.n/a'),
                    'expire_time' => \Carbon\Carbon::parse($subscription->ends_at)->translatedFormat('h:i A') ?? __('messages.common.n/a'),
                    'sms_limit' => $subscription->sms_limit ?? __('messages.common.n/a'),
                    'status' => $subscription->status == 1 ? 'Active' : 'Deactive'
            ];
        }

        return $this->sendResponse($data, 'Subscription Retrieved Successfully');
    }

    public function editSubscriptions($id)
    {
        $subscription = Subscription::find($id);
        if (empty($subscription)) {
            return $this->sendError('Subscription not found');
        }
        return $this->sendResponse($subscription, 'Subscription Retrieved Successfully');
    }

    public function updateSubscriptions(Request $request, $id)
    {
        $input = $request->all();
        $subscription = Subscription::findOrFail($id);

        if ($subscription->status == Subscription::INACTIVE) {
            $input['status'] = Subscription::ACTIVE;
            $subscription->update($input);
        } else {
            $subscription->update($input);
        }

        return $this->sendSuccess('Subscription updated successfully.');
    }

    public function filter(Request $request) : \Illuminate\Http\JsonResponse
    {
        $status = $request->get('status');
        $subscriptionsQuery = Subscription::with(['subscriptionPlan', 'user']);
        if($status == 'all') {
            $subscriptions = $subscriptionsQuery->orderBy('id', 'desc')->get();

            foreach($subscriptions as $subscription){
                $data[] = $subscription->preperSubscription();
            }
            return $this->sendResponse($data, 'Subscription Retrieved Successfully');
        }elseif($status == 'deactive') {
            $subscriptions = $subscriptionsQuery->whereStatus(0)->orderBy('id', 'desc')->get();
            foreach($subscriptions as $subscription){
                $data[] = $subscription->preperSubscription();
            }
            return $this->sendResponse($data, 'Subscription Retrieved Successfully');
        }elseif($status == 'active') {
            $subscriptions = $subscriptionsQuery->whereStatus(1)->orderBy('id', 'desc')->get();
            foreach($subscriptions as $subscription){
                $data[] = $subscription->preperSubscription();
            }
            return $this->sendResponse($data, 'Subscription Retrieved Successfully');
        }else{
            return $this->sendError('Subscription not found');
        }
    }
}
