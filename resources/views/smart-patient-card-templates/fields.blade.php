    <div class="row">
    <div class="col-xl-4 col-md-3">
        <div class="row">
            <div class="col-md-11">
                <div class="form-group mb-5">
                    {{ Form::label('template_name', __('messages.lunch_break.template_name') . ':', ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{ Form::text('template_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.lunch_break.template_name')]) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-5">
                    {{ Form::label('header_color', __('messages.lunch_break.header_color') . ':', ['class' => 'form-label required']) }}
                    <div class="header-color-wrapper"></div>
                    {{ Form::hidden('header_color', '#161e54', ['id' => 'headerColorId', 'class' => 'form-control color']) }}
                    <span id="secondaryColorError" class="text-danger"></span>
                </div>
                {{ Form::hidden('header_color','', ['id' => 'editHeaderColorId']) }}
            </div>
            <div class="col-md-8">
                <div class="form-group mb-5">
                    {{ Form::label('show_email', __('messages.lunch_break.show_email') . ':', ['class' => 'form-label ']) }}
                    <br>
                    <div class="form-check form-switch">
                        <input name="show_email" class="form-check-input patient-email" value="1" type="checkbox"
                            id="showEmail" checked>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-5">
                    {{ Form::label('show_phone', __('messages.lunch_break.show_phone') . ':', ['class' => 'form-label']) }}
                    <br>
                    <div class="form-check form-switch">
                        <input name="show_phone" class="form-check-input is-active" value="1" type="checkbox"
                            id="showPhone" checked>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-5">
                    {{ Form::label('show_dob', __('messages.lunch_break.show_dob') . ':', ['class' => 'form-label']) }}
                    <br>
                    <div class="form-check form-switch">
                        <input name="show_dob" class="form-check-input is-active" value="1" type="checkbox"
                            id="showDob" checked>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-5">
                    {{ Form::label('show_blood_group', __('messages.lunch_break.show_blood_group') . ':', ['class' => 'form-label']) }}
                    <br>
                    <div class="form-check form-switch">
                        <input name="show_blood_group" class="form-check-input is-active" value="1" type="checkbox"
                            id="showBloodGroup" checked>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-5">
                    {{ Form::label('show_address', __('messages.lunch_break.show_address') . ':', ['class' => 'form-label']) }}
                    <br>
                    <div class="form-check form-switch">
                        <input name="show_address" class="form-check-input is-active" value="1" type="checkbox"
                            id="showAddress" checked>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group mb-5">
                    {{ Form::label('show_patient_unique_id', __('messages.lunch_break.show_patient_unique_id') . ':', ['class' => 'form-label']) }}
                    <br>
                    <div class="form-check form-switch">
                        <input name="show_patient_unique_id" class="form-check-input is-active" value="1"
                            type="checkbox" id="showUniqueID" checked>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-md-7">
        <div class="d-flex align-items-start justify-content-start">
            <div class="patient-card shadow-lg">
                <div class="card-header d-flex align-items-center custom-card-header">
                    <div class="flex-1 d-flex align-items-center me-3">
                        <div class="logo me-4">
                            <img src="{{ getLogoUrl() }}" alt="logo" class="h-100 img-fluid" />
                        </div>
                        <h4 class="text-white mb-0 fw-bold">{{ getAppName() }}</h4>
                    </div>
                    <div class="flex-1 text-end">
                        <address class="text-white fs-12 mb-0">
                            <p class="mb-0">
                                {{ getSettingValueByKey('hospital_address') }}
                            </p>
                        </address>
                    </div>
                </div>
                <div class="patient-card-body bg-white">
                    <div class="d-flex justify-content-between">
                        <div class="">
                            <div class="d-flex mb-3">
                                <div class="image image-circle image-small me-3">
                                    <a>
                                        <div>
                                            <img src="{{ asset('assets/images/profile.png') }}" alt=""
                                                 class="user-img rounded-circle image">
                                        </div>
                                    </a>
                                </div>
                                <div class="">
                                    <table class="table table-borderless patient-desc mb-0">
                                        <tr id="cardName">
                                            <td class="pe-3">{{ __('messages.bed.name') }}:</td>
                                            <td>James Bond</td>
                                        </tr>
                                        <tr id="patientEmail">
                                            <td class="pe-3">{{ __('auth.email') }}:</td>
                                            <td>marian@gmail.com</td>
                                        </tr>
                                        <tr id="patientNumber">
                                            <td class="pe-3">{{ __('messages.enquiry.contact') }}:</td>
                                            <td>1234567890</td>
                                        </tr>
                                        <tr id="patientDob">
                                            <td class="pe-3">{{ __('messages.lunch_break.dob') }}:</td>
                                            <td>25/02/2006</td>
                                        </tr>
                                        <tr id="patientBloodGroup">
                                            <td class="pe-3">{{ __('messages.user.blood_group') }}:</td>
                                            <td>A+</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex address-text me-5" id="patientAddress">
                                <div class="mb-0 me-3">{{ __('messages.common.address') }}:</div>
                                <div>
                                    <span class="mb-0">
                                        D.No.1 Street name Address line 2 line 3
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="w-25">
                            <div class="text-end mb-5">
                                <div class="qr-code ms-auto">
                                    {!! QrCode::size(90)->generate('https://hms-saas.test/h/sims/patient-details/700XYs') !!}
                                </div>
                                <h6 class="text-primary text-end mt-5" id="patientUniqueID">{{ __('messages.lunch_break.id') }}:1001</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="col-xl-8 col-md-9 mb-md-0 mb-5 px-0">
        <div class="d-flex align-items-start justify-content-center">
            <div class="patient-card shadow-lg">
                <div class="patient-card-header d-flex align-items-center custom-card-header">
                    <div class="flex-1 d-flex align-items-center me-3">
                        <div class="patient-card-logo me-4">
                            <img src="{{ asset(getLogoUrl()) }}" alt="logo" class="h-100 img-fluid" />
                        </div>
                        <h4 class="text-white mb-0 fw-bold">{{ getAppName() }}</h4>
                    </div>
                    <div class="flex-1 text-end">
                        <address class="text-white fs-12 mb-0">
                            <p class="mb-0">
                                {{ getSettingValueByKey('hospital_address') }}
                            </p>
                        </address>
                    </div>
                </div>
                <div class="patient-card-body bg-white">
                    <div class="d-flex flex-sm-nowrap flex-wrap justify-content-between">
                        <div class="">
                            <div class="d-flex flex-sm-nowrap flex-wrap mb-3">
                                <div class="image image-circle image-small me-3">
                                    <a>
                                        <div>
                                            <img src="{{ asset('assets/images/profile.png') }}" alt=""
                                                class="user-img rounded-circle image">
                                        </div>
                                    </a>
                                </div>
                                <div class="">
                                    <table class="table table-borderless patient-desc mb-0">
                                        <tr id="cardName">
                                            <td class="pe-0">{{ __('messages.bed.name') }}:</td>
                                            <td class="pe-0">James Bond</td>
                                        </tr>
                                        <tr id="patientEmail">
                                            <td class="pe-0">{{ __('auth.email') }}:</td>
                                            <td class="pe-0">marian@gmail.com</td>
                                        </tr>
                                        <tr id="patientNumber">
                                            <td class="pe-0">{{ __('messages.enquiry.contact') }}:</td>
                                            <td class="pe-0">1234567890</td>
                                        </tr>
                                        <tr id="patientDob">
                                            <td class="pe-0">{{ __('messages.lunch_break.dob') }}:</td>
                                            <td class="pe-0">25/02/2006</td>
                                        </tr>
                                        <tr id="patientBloodGroup">
                                            <td class="pe-0">{{ __('messages.user.blood_group') }}:</td>
                                            <td class="pe-0">A+</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex address-text me-5" id="patientAddress">
                                <div class="mb-0 me-3">{{ __('messages.common.address') }}:</div>
                                <div>
                                    <address class="mb-0">
                                        D.No.1 Street name Address line 2 line 3
                                    </address>
                                </div>
                            </div>
                        </div>

                        <div class="w-25">
                            <div class="text-end mb-5">
                                <div class="qr-code ms-auto">
                                    {!! QrCode::size(90)->generate('https://hms-saas.test/h/sims/patient-details/700XYs') !!}
                                </div>
                                <h6 class="text-primary text-end mt-5" id="patientUniqueID">
                                    {{ __('messages.lunch_break.id') }}:1001</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-start">
        <div class="d-flex ">
            {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary']) }}
            <a href="{{ route('patient-smart-card-templates.index') }}"
                class="btn btn-secondary ms-2">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
</div>
