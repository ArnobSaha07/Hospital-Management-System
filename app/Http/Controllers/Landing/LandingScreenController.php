<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\AppBaseController;
use App\Models\AdminTestimonial;
use App\Models\Faqs;
use App\Models\HospitalType;
use App\Models\LandingAboutUs;
use App\Models\SectionFive;
use App\Models\SectionFour;
use App\Models\SectionOne;
use App\Models\SectionThree;
use App\Models\SectionTwo;
use App\Models\ServiceSlider;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class LandingScreenController extends AppBaseController
{
    /**
     * @return Factory|View
     */
    public function index(): View
    {
        $data['phone'] = getSuperAdminSettingKeyValue('phone');
        $data['sectionOne'] = SectionOne::first();
        $data['sectionTwo'] = SectionTwo::first();
        $data['sectionThree'] = SectionThree::first();
        $data['sectionFour'] = SectionFour::first();
        $data['sectionFive'] = SectionFive::first();

        //        $data['subscriptionPricingMonthPlans'] = SubscriptionPlan::with(['plan', 'plans', 'planFeatures.feature'])
        //            ->where('frequency', '=', SubscriptionPlan::MONTH)
        //            ->get();

        $plans = SubscriptionPlan::with([
            'plan', 'plans', 'planFeatures.feature' => function ($query) {
                $query->select('id', 'name');
            },
        ])->get();

        $data['subscriptionPricingMonthPlans'] = [];
        $data['subscriptionPricingYearPlans'] = [];

        if (! empty($plans)) {
            foreach ($plans as $plan) {
                if ($plan->frequency == SubscriptionPlan::MONTH) {
                    $data['subscriptionPricingMonthPlans'][] = $plan;
                } elseif ($plan->frequency == SubscriptionPlan::YEAR) {
                    $data['subscriptionPricingYearPlans'][] = $plan;
                }
            }
        }

        //        $data['subscriptionPricingMonthPlans'] = SubscriptionPlan::with([
        //            'plan', 'plans', 'planFeatures.feature' => function ($query) {
        //                $query->select('id', 'name');
        //            },
        //        ])->where('frequency', '=', SubscriptionPlan::MONTH)
        //            ->get();
        //        $data['subscriptionPricingYearPlans'] = SubscriptionPlan::with([
        //            'plan', 'plans', 'planFeatures.feature' => function ($query) {
        //                $query->select('id', 'name');
        //            },
        //        ])->where('frequency', '=', SubscriptionPlan::YEAR)
        //            ->get();

        $data['hospitals'] = User::with(['department', 'media'])
            ->where('id', '!=', getLoggedInUserId())
            ->where('department_id', '=', User::USER_ADMIN)
            ->whereNotNull('hospital_name')->paginate(9, '*', 'hospitals');

        return view('landing.home.index')->with($data);
    }

    /**
     * @return Factory|View
     */
    public function aboutUs(): View
    {
        $data['sectionFour'] = SectionFour::first();
        $data['sectionFive'] = SectionFive::first();
        $data['landingAboutUs'] = LandingAboutUs::first();
        $data['faqs'] = Faqs::take(3)->orderByDesc('created_at')->get();

        $plans = SubscriptionPlan::with([
            'plan', 'plans', 'planFeatures.feature' => function ($query) {
                $query->select('id', 'name');
            },
        ])->get();

        $data['subscriptionPricingMonthPlans'] = [];
        $data['subscriptionPricingYearPlans'] = [];

        if (! empty($plans)) {
            foreach ($plans as $plan) {
                if ($plan->frequency == SubscriptionPlan::MONTH) {
                    $data['subscriptionPricingMonthPlans'][] = $plan;
                } elseif ($plan->frequency == SubscriptionPlan::YEAR) {
                    $data['subscriptionPricingYearPlans'][] = $plan;
                }
            }
        }

        return view('landing.home.about_us')->with($data);
    }

    /**
     * @return Factory|View
     */
    public function services(): View
    {
        $data['sectionFour'] = SectionFour::first();

        $plans = SubscriptionPlan::with([
            'plan', 'plans', 'planFeatures.feature' => function ($query) {
                $query->select('id', 'name');
            },
        ])->get();

        $data['subscriptionPricingMonthPlans'] = [];
        $data['subscriptionPricingYearPlans'] = [];

        if (! empty($plans)) {
            foreach ($plans as $plan) {
                if ($plan->frequency == SubscriptionPlan::MONTH) {
                    $data['subscriptionPricingMonthPlans'][] = $plan;
                } elseif ($plan->frequency == SubscriptionPlan::YEAR) {
                    $data['subscriptionPricingYearPlans'][] = $plan;
                }
            }
        }

        $data['serviceSlider'] = ServiceSlider::get();
        $data['testimonials'] = AdminTestimonial::get();

        return view('landing.home.services')->with($data);
    }

    /**
     * @return Factory|View
     */
    public function pricing(): View
    {
        $plans = SubscriptionPlan::with([
            'plan', 'plans', 'planFeatures.feature' => function ($query) {
                $query->select('id', 'name');
            },
        ])->get();

        $data['subscriptionPricingMonthPlans'] = [];
        $data['subscriptionPricingYearPlans'] = [];

        if (! empty($plans)) {
            foreach ($plans as $plan) {
                if ($plan->frequency == SubscriptionPlan::MONTH) {
                    $data['subscriptionPricingMonthPlans'][] = $plan;
                } elseif ($plan->frequency == SubscriptionPlan::YEAR) {
                    $data['subscriptionPricingYearPlans'][] = $plan;
                }
            }
        }

        return view('landing.home.pricing')->with($data);
    }

    /**
     * @return Factory|View
     */
    public function contactUs(): View
    {
        return view('landing.home.contact_us');
    }

    public function faq(): View
    {
        $faqs = Faqs::take(6)->orderByDesc('created_at')->get();

        return view('landing.home.faq', compact('faqs'));
    }

    /**
     * @return Factory|View
     */
    public function hospitals(): View
    {
        $data['hospitals'] = User::with(['department', 'media'])
            ->where('id', '!=', getLoggedInUserId())
            ->where('department_id', '=', User::USER_ADMIN)
            ->whereNotNull('username')
            ->whereNotNull('hospital_name')->paginate(9, '*', 'hospitals');

        return view('landing.home.hospitals', $data);
    }

    /**
     * @return Factory|View
     */
    public function login(): View
    {
        return view('landing.home.login');
    }

    /**
     * @return Factory|View
     */
    public function register(): View
    {
        return view('landing.home.register');
    }
}
