<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Http\Requests\UpdateSuperAdminFooterSettingRequest;
use App\Http\Requests\UpdateSuperAdminSettingRequest;
use App\Models\Module;
use App\Models\SuperAdminCurrencySetting;
use App\Models\SuperAdminSetting;
use App\Repositories\SettingRepository;
use Flash;
use http\Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\DiskDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileIsTooBig;
use Spatie\MediaLibrary\Exceptions\MediaCannotBeDeleted;

class SettingController extends AppBaseController
{
    /** @var SettingRepository */
    private $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }

    /**
     * Show the form for editing the specified Setting.
     *
     * @return Factory|View
     */
    public function edit(Request $request): View
    {
        $settings = $this->settingRepository->getSyncList();
        $currencies = getCurrencies();
        $statusArr = Module::STATUS_ARR;
        $sectionName = ($request->section === null) ? 'general' : $request->section;

        return view("settings.$sectionName", compact('currencies', 'settings', 'statusArr', 'sectionName'));
    }

    /**
     * Update the specified Setting in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws MediaCannotBeDeleted
     */
    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->settingRepository->updateSetting($request->all());

        Flash::success(__('messages.flash.setting_updated'));

        return redirect(route('settings.edit'));
    }

    /**
     * Display a listing of the Module.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function getModule(Request $request)
    {
    }

    public function activeDeactiveStatus(Module $module): JsonResponse
    {
        $is_active = ! $module->is_active;
        $module->update(['is_active' => $is_active]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    /**
     * Show the form for editing the specified Setting.
     *
     * @return Factory|View
     */
    public function editSuperAdminSettings(Request $request): View
    {
        $settings = $this->settingRepository->getSyncListForSuperAdmin();
        $currencies = SuperAdminCurrencySetting::all();
        $sectionName = ($request->section === null) ? 'general' : $request->section;

        return view("super_admin_settings.$sectionName", compact('settings', 'sectionName', 'currencies'));
    }

    /**
     * Update the specified Setting in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws MediaCannotBeDeleted
     */
    public function updateSuperAdminSettings(UpdateSuperAdminSettingRequest $request): RedirectResponse
    {
        $this->settingRepository->updateSuperAdminSetting($request->all());

        Flash::success(__('messages.flash.setting_updated'));

        return redirect(route('super.admin.settings.edit'));
    }

    /**
     * @return Factory|View
     */
    public function editSuperAdminFooterSettings(): View
    {
        $settings = SuperAdminSetting::pluck('value', 'key')->toArray();

        return view('super_admin_footer_settings.index', compact('settings'));
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function updateSuperAdminFooterSettings(UpdateSuperAdminFooterSettingRequest $request): RedirectResponse
    {
        $this->settingRepository->updateSuperFooterAdminSetting($request->all());

        Flash::success(__('messages.flash.setting_updated'));

        return redirect(route('super.admin.footer.settings.edit'));
    }

    /**
     * @return Factory|View
     */
    public function editPaymentSettings(): View
    {
        $setting = SuperAdminSetting::pluck('value', 'key')->toArray();

        return view('super_admin_settings.Credentials', compact('setting'));
    }

    /**
     * @return Factory|View
     */
    public function updatePaymentSettings(Request $request): RedirectResponse
    {
        $this->settingRepository->updatePaymentSettings($request->all());

        Flash::success(__('messages.flash.setting_updated'));

        return redirect(route('super-admin-payment-gateway.edit'));
    }
}
