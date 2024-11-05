<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSubscriptionPlanRequest;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use App\Mail\NotifyMailSuperAdminForSubscribeHospital;
use App\Models\Feature;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Repositories\SubscriptionPlanRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;

class SubscriptionPlanController extends AppBaseController
{
    private $subscriptionPlanRepository;

    public function __construct(SubscriptionPlanRepository $subscriptionPlanRepo)
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepo;
    }

    /**
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): \Illuminate\View\View
    {
        return view('super_admin.subscription_plans.index');
    }

    /**
     * Show the form for creating a new Service.
     *
     * @return Factory|\Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $planType = SubscriptionPlan::PLAN_TYPE;
        $planFeatures = Feature::HasParent()->IsDefault()->get();

        return view('super_admin.subscription_plans.create', compact('planType', 'planFeatures'));
    }

    public function store(CreateSubscriptionPlanRequest $request): RedirectResponse
    {
        $input = $request->all();
        $this->subscriptionPlanRepository->store($input);
        Flash::success(__('messages.flash.subscription_plan_saved'));

        return redirect(route('super.admin.subscription.plans.index'));
    }

    public function edit(SubscriptionPlan $subscriptionPlan): \Illuminate\View\View
    {
        $planType = SubscriptionPlan::PLAN_TYPE;
        $planFeatures = Feature::HasParent()->IsDefault()->get();
        $subscriptionPlanFeatures = $subscriptionPlan->features()->pluck('feature_id')->toArray();

        return view('super_admin.subscription_plans.edit',
            compact('subscriptionPlan', 'planType', 'planFeatures', 'subscriptionPlanFeatures'));
    }

    public function update(UpdateSubscriptionPlanRequest $request, SubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        $input = $request->all();
        $this->subscriptionPlanRepository->update($input, $subscriptionPlan->id);
        Flash::success(__('messages.flash.subscription_plan_updated'));

        return redirect(route('super.admin.subscription.plans.index'));
    }

    /**
     * @return Application|Factory|View
     */
    public function show(SubscriptionPlan $subscriptionPlan): \Illuminate\View\View
    {
        $subscriptionPlan->load(['subscription', 'features']);

        return view('super_admin.subscription_plans.show', compact('subscriptionPlan'));
    }

    /**
     * @return mixed
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $result = Subscription::where('subscription_plan_id', $subscriptionPlan->id)->where('status',
            Subscription::ACTIVE)->count();
        if ($result > 0) {
            return $this->sendError(__('messages.flash.subscription_plan_cant_deleted'));
        }
        $subscriptionPlan->delete();

        return $this->sendSuccess(__('messages.flash.subscription_plan_deleted'));
    }

    /**
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function showTransactionsLists(Request $request): \Illuminate\View\View
    {
        $paymentTypes = Transaction::PAYMENT_TYPES;

        return view('subscription_transactions.index', compact('paymentTypes'));
    }

    /**
     * @return Application|Factory|View
     */
    public function viewTransaction(Subscription $subscription): \Illuminate\View\View
    {
        $subscription->load(['subscriptionPlan', 'user']);

        return view('subscription_transactions.show', compact('subscription'));
    }

    public function makePlanDefault(int $id): JsonResponse
    {
        $defaultSubscriptionPlan = SubscriptionPlan::where('is_default', 1)->first();
        $defaultSubscriptionPlan->update(['is_default' => 0]);
        $subscriptionPlan = SubscriptionPlan::findOrFail($id);
        if ($subscriptionPlan->trial_days == 0) {
            $subscriptionPlan->trial_days = SubscriptionPlan::TRAIL_DAYS;
        }
        $subscriptionPlan->is_default = 1;
        $subscriptionPlan->save();

        return $this->sendSuccess(__('messages.flash.default_plan_changed'));
    }

    /**
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function showSubscriptionsLists(Request $request): \Illuminate\View\View
    {
        $status = subscription::STATUS_ARR;
        $planType = SubscriptionPlan::PLAN_TYPE;

        return view('subscription.index', compact('status', 'planType'));
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function showSubscriptions($id): \Illuminate\View\View
    {
        $subscription = Subscription::with('SubscriptionPlan', 'user')->findOrFail($id);

        return view('subscription.show', compact('subscription'));
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function editSubscriptions($id): \Illuminate\View\View
    {
        $subscription = Subscription::with('SubscriptionPlan', 'transactions', 'user')->findOrFail($id);
        $status = subscription::STATUS_ARR;

        return view('subscription.edit', compact('subscription', 'status'));
    }

    /**
     * @return Application|Factory|View
     */
    public function updateSubscriptions(Request $request, $id): RedirectResponse
    {
        $input = $request->all();
        $subscription = Subscription::findOrFail($id);

        if ($subscription->status == Subscription::INACTIVE) {
            $input['status'] = Subscription::ACTIVE;
            $subscription->update($input);
        } else {
            $subscription->update($input);
        }

        Flash::success(__('messages.flash.subscription_plan_deleted'));

        return redirect(route('subscriptions.list.index'));
    }

    public function changePaymentStatus(Request $request): JsonResponse
    {
        $input = $request->all();
        $transaction = Transaction::with('transactionSubscription', 'user')->findOrFail($input['id']);

        if ($input['status'] == Transaction::APPROVED) {
            $subscription = $transaction->transactionSubscription;
            //
            //            $transaction->update([
            //                'is_manual_payment' => $input['status'],
            //                'status'            => Subscription::ACTIVE,
            //            ]);
            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update([
                    'is_manual_payment' => $input['status'],
                    'status' => Subscription::ACTIVE,
                    'tenant_id' => $transaction->user->tenant_id,
                ]);

            Subscription::findOrFail($subscription->id)->update(['status' => Subscription::ACTIVE]);
            // De-Active all other subscription
            Subscription::whereUserId($subscription->user_id)
                ->where('id', '!=', $subscription->id)
                ->update([
                    'status' => Subscription::INACTIVE,
                ]);

            $subscription->update(['status', Subscription::ACTIVE]);

            $mailData = [
                'amount' => $subscription->plan_amount,
                'user_name' => $subscription->user->full_name,
                'plan_name' => $subscription->subscriptionPlan->name,
                'start_date' => $subscription->starts_at,
                'end_date' => $subscription->ends_at,
            ];

            Mail::to($subscription->user->email)
            ->send(new NotifyMailSuperAdminForSubscribeHospital('emails.hospital_subscription_mail',
            __('messages.new_change.subscription_mail'),
                $mailData));

            return $this->sendSuccess(__('messages.flash.manual_payment_approved'));
        } else {
            if ($input['status'] == Transaction::DENIED) {
                $subscription = $transaction->transactionSubscription;

                //                $transaction->update([
                //                    'is_manual_payment' => $input['status'],
                //                    'status'            => Subscription::INACTIVE,
                //                    'tenant_id'         => $transaction->user->tenant_id,
                //                ]);
                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'is_manual_payment' => $input['status'],
                        'status' => Subscription::INACTIVE,
                        'tenant_id' => $transaction->user->tenant_id,
                    ]);

                //                $subscription->update(['status', Subscription::INACTIVE]);
                $subscription->delete();

                return $this->sendSuccess(__('messages.flash.manual_payment_denied'));
            }
        }
    }
}
