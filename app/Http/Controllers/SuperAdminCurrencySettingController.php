<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdminCurrencySettingRequest;
use App\Http\Requests\UpdateAdminCurrencySettingRequest;
use App\Models\SubscriptionPlan;
use App\Models\SuperAdminCurrencySetting;
use App\Repositories\currencySettingRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class SuperAdminCurrencySettingController extends AppBaseController
{
    /** @var currencySettingRepository */
    public $currencySettingRepository;

    public function __construct(currencySettingRepository $currencySettingRepo)
    {
        $this->currencySettingRepository = $currencySettingRepo;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        $currencySettings = SuperAdminCurrencySetting::all();

        return view('super_admin_currency_settings.index')->with('currencySettings', $currencySettings);
    }

    public function create()
    {
        //
    }

    public function store(CreateAdminCurrencySettingRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->currencySettingRepository->storeAdminCurrencies($input);

        return $this->sendSuccess(__('messages.new_change.currency_store'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id): JsonResponse
    {
        $currencySetting = SuperAdminCurrencySetting::find($id);

        return $this->sendResponse($currencySetting, 'Currency retrieved successfully.');
    }

    public function update(UpdateAdminCurrencySettingRequest $request, $id): JsonResponse
    {
        $input = $request->all();

        $this->currencySettingRepository->updateAdminCurrency($input, $id);

        return $this->sendSuccess(__('messages.new_change.currency_update'));
    }

    /**
     * @throws Exception
     */
    public function destroy($id): JsonResponse
    {
        $superAdminCurrencySetting = SuperAdminCurrencySetting::find($id);
        $model = SubscriptionPlan::class;
        $result = canCurrencyDelete($model, 'currency', $superAdminCurrencySetting->currency_code);
        if ($result) {
            return $this->sendError(__('messages.new_change.currency_not_delete'));
        }

        $superAdminCurrencySetting->delete();

        return $this->sendSuccess('Currency deleted');
    }
}
