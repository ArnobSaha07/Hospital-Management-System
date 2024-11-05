<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatecurrencySettingRequest;
use App\Http\Requests\UpdatecurrencySettingRequest;
use App\Models\CurrencySetting;
use App\Models\Setting;
use App\Repositories\currencySettingRepository;
use Flash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencySettingController extends AppBaseController
{
    /** @var currencySettingRepository */
    private $currencySettingRepository;

    public function __construct(currencySettingRepository $currencySettingRepo)
    {
        $this->currencySettingRepository = $currencySettingRepo;
    }

    /**
     * Display a listing of the currencySetting.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request): View
    {
        $currencySettings = $this->currencySettingRepository->all();

        return view('currency_settings.index')
            ->with('currencySettings', $currencySettings);
    }

    /**
     * Show the form for creating a new currencySetting.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(): View
    {
        return view('currency_settings.create');
    }

    /**
     * Store a newly created currencySetting in storage.
     */
    public function store(CreatecurrencySettingRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->currencySettingRepository->create($input);

        return $this->sendSuccess(__('messages.new_change.currency_store'));
    }

    /**
     * Display the specified currencySetting.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show(int $id)
    {
        $currencySetting = $this->currencySettingRepository->find($id);

        if (empty($currencySetting)) {
            Flash::error(__('messages.new_change.currency_setting_not_found'));

            return redirect(route('currencySettings.index'));
        }

        return view('currency_settings.show')->with('currencySetting', $currencySetting);
    }

    /**
     * Show the form for editing the specified currencySetting.
     */
    public function edit(CurrencySetting $currencySetting): JsonResponse
    {
        if (! canAccessRecord(CurrencySetting::class, $currencySetting->id)) {
            return $this->sendError(__('messages.flash.currency_not_found'));
        }

        return $this->sendResponse($currencySetting, 'Currency retrieved successfully.');
    }

    /**
     * Update the specified currencySetting in storage.
     */
    public function update(CurrencySetting $currencySetting, UpdatecurrencySettingRequest $request): JsonResponse
    {
        $defaultCurrency = getCurrentCurrency();

        $currencyCode = strtolower($currencySetting->currency_code);

        if ($defaultCurrency == $currencyCode) {
            Setting::where('key', '=', 'current_currency')
                ->first()
                ->update(['value' => strtolower($request->currency_code)]);
        }

        $input = $request->all();

        $this->currencySettingRepository->update($input, $currencySetting->id);

        return $this->sendSuccess(__('messages.new_change.currency_update'));
    }

    /**
     * Remove the specified currencySetting from storage.
     *
     *
     * @throws \Exception
     */
    public function destroy(CurrencySetting $currencySetting): JsonResponse
    {
        if (! canAccessRecord(CurrencySetting::class, $currencySetting->id)) {
            return $this->sendError(__('messages.flash.currency_not_found'));
        }

        $currency = Setting::where('key', 'current_currency')->first()->value;
        if ($currency == strtolower($currencySetting->currency_code)) {
            return $this->sendError(__('messages.new_change.default_currency_not_delete'));
        } else {
            $this->currencySettingRepository->delete($currencySetting->id);

            return $this->sendSuccess('Currency deleted');
        }
    }
}
