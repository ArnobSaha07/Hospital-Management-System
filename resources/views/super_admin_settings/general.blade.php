@extends('super_admin_settings.edit')
@section('title')
    {{ __('messages.general') }}
@endsection
@section('section')
    <div class="card border-0">
        <div class="card-body">
            {{ Form::open(['route' => ['super.admin.settings.update'], 'method' => 'post', 'files' => true, 'id' => 'createSetting']) }}
            <div class="alert alert-danger d-none hide" id="validationErrorsBox"></div>
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group mb-5">
                        {{ Form::label('app_name', __('messages.setting.app_name') . ':', ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('app_name', $settings['app_name'], ['class' => 'form-control', 'maxLength' => 30, 'placeholder' => __('messages.setting.app_name')]) }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-5">
                        {{ Form::label('plan_expire_notification', __('messages.plan_expire_notifications') . ':', ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('plan_expire_notification', $settings['plan_expire_notification'], ['class' => 'form-control', 'maxLength' => 2, 'placeholder' => __('messages.plan_expire_notifications')]) }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        {{ Form::label('default_country_code', __('messages.common.default_country_code') . ':', ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('default_country_code', null, ['class' => 'form-control', 'placeholder' => __('Default Country Code'), 'id' => 'defaultCountryData']) }}
                        {{ Form::hidden('default_country_code', $settings['default_country_code'], ['id' => 'defaultCountryCode']) }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        {{ Form::label('super_admin_currency', __('messages.setting.currency'), ['class' => 'form-label']) }}
                        <span class="required"></span>
                        <select id="currencyType" data-show-content="true" class="form-select form-select-solid"
                            name="super_admin_currency">
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency->currency_code }}"
                                    {{ strtoupper($settings['super_admin_currency']) == $currency->currency_code ? 'selected' : '' }}>
                                    {{ $currency->currency_icon }}&nbsp;&nbsp;&nbsp; {{ $currency->currency_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- App Enable Recaptcha field -->

                <div class="form-group col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::label('google_captcha_key', __('messages.setting.enable_google_reCAPTCHA') . ':', ['class' => 'form-label']) }}
                            <div class="form-check form-switch fv-row mb-3">
                                <input tabindex="11" name="enable_google_recaptcha"
                                    class="form-check-input w-35px h-20px is-active" value="1"
                                    {{ isset($settings['enable_google_recaptcha']) && $settings['enable_google_recaptcha'] ? 'checked' : '' }}
                                    type="checkbox" id="captchaEnableAdmin">
                                <label class="form-check-label" for="allowmarketing"></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{ Form::label('default_language', __('messages.profile.language') . ':', ['class' => 'form-label']) }}
                            {{ Form::select('default_language', \App\Models\User::LANGUAGES, isset($settings['default_language']) ? $settings['default_language'] : '', ['id' => 'defaultLanguage', 'class' => 'form-select', 'data-control' => 'select2', 'data-hide-search' => 'true', 'data-placeholder' => 'language']) }}
                        </div>
                    </div>
                </div>

                <div class="captcha-div d-none col-12">
                    <div class="row">
                        <div class="form-group col-sm-6 mb-5">
                            {{ Form::label('google_captcha_key', __('messages.new_change.captcha_key') . ':', ['class' => 'form-label']) }}
                            {{ Form::text('google_captcha_key', isset($settings['google_captcha_key']) ? $settings['google_captcha_key'] : null, ['class' => 'form-control', 'id' => 'captchaKey', 'placeholder' => __('Google Captcha key')]) }}

                        </div>
                        <div class="form-group col-sm-6 mb-5">
                            {{ Form::label('google_captcha_secret', __('messages.new_change.captcha_secret') . ':', ['class' => 'form-label']) }}
                            {{ Form::text('google_captcha_secret', isset($settings['google_captcha_secret']) ? $settings['google_captcha_secret'] : null, ['class' => 'form-control', 'id' => 'captchaSecret', 'placeholder' => __('Google Captcha Secret')]) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-5">
                        {{ Form::label('manual_instruction', __('messages.custom_field.manual_instruction') . ':', ['class' => 'form-label']) }}
                        {{ Form::textarea('manual_instruction', $settings['manual_instruction'], ['class' => 'form-control', 'tabindex' => '1', 'rows' => '5', 'maxLength' => 191, 'placeholder' => __('messages.custom_field.manual_instruction')]) }}
                    </div>
                </div>
                <!-- App Logo Field -->
                <div class="form-group col-sm-2 mb-5">
                    <div class="row2">
                        <div class="d-block">
                            {{ Form::label('app_logo', __('messages.setting.app_logo') . ':', ['class' => 'form-label']) }}
                            <span data-bs-toggle="tooltip" data-placement="top"
                                data-bs-original-title="{{ __('messages.setting.image_validation') }}">
                                <i class="fas fa-question-circle ml-1 mt-1 general-question-mark"></i>
                            </span>
                        </div>
                        <div class="d-block">
                            <?php
                            $style = 'style=';
                            $background = 'background-image:';
                            ?>
                            <div class="image-picker">
                                <div class="image previewImage" id="previewImage"
                                    style="background-image: url('{{ $settings['app_logo'] ? asset($settings['app_logo']) : asset('hms-saas-logo.png') }}')">
                                </div>
                                <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                                    data-placement="top" data-bs-original-title={{ __('messages.setting.change_logo') }}>
                                    <label>
                                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                        {{ Form::file('app_logo', ['id' => 'appLogos', 'class' => 'image-upload d-none', 'accept' => 'image/*']) }}
                                        <input type="hidden" name="avatar_remove">
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Favicon Field -->
                <div class="form-group col-sm-2 mb-5">
                    <div class="row2">
                        <div class="d-block">
                            {{ Form::label('favicon', __('messages.setting.favicon') . ':', ['class' => 'form-label']) }}
                            <span class="required"></span>
                            <span data-bs-toggle="tooltip" data-placement="top"
                                data-bs-original-title="{{ __('messages.setting.favicon_validation') }}">
                                <i class="fas fa-question-circle ml-1 mt-1 general-question-mark"></i>
                            </span>
                        </div>
                        <div class="d-block">
                            <?php
                            $style = 'style=';
                            $background = 'background-image:';
                            ?>
                            <div class="image-picker">
                                <div class="image previewImage" id="previewImage"
                                    style="background-image: url('{{ isset($settings['favicon']) ? asset($settings['favicon']) : asset('web/img/hms-saas-favicon.ico') }}')">
                                </div>
                                <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                                    data-placement="top"
                                    data-bs-original-title={{ __('messages.setting.change_favicon') }}>
                                    <label>
                                        <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                        {{ Form::file('favicon', ['id' => 'favicons', 'class' => 'image-upload d-none', 'accept' => 'image/*']) }}
                                        <input type="hidden" name="avatar_remove">
                                    </label>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="float-end">
                {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2']) }}
                {{ Form::reset(__('messages.common.cancel'), ['class' => 'btn btn-secondary']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection
