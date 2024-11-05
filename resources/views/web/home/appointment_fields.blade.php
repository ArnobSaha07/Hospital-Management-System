<?php
$style = 'style=';
$display = 'display:';
?>
<div class="d-lg-flex align-items-center justify-content-between mb-4">
    <h2 class="mb-3">{{ __('messages.web_appointment.make_an_appointment') }}</h2>
    <div class="d-flex align-items-center mb-3">
        <div class="form-check d-flex align-items-center mb-0">
            {{ Form::radio('patient_type', '1', true, ['class' => 'form-check-input new-patient-radio', 'id' => 'newPatient1']) }}
            <label class="form-check-label ms-3" for="newPatient1">
                {{ __('messages.new_patient') }}
            </label>
        </div>
        <div class="form-check ms-4 d-flex align-items-center mb-0">
            {{ Form::radio('patient_type', '2', false, ['class' => 'form-check-input old-patient-radio', 'id' => 'oldPatient1']) }}

            <label class="form-check-label ms-3" for="oldPatient1">
                {{ __('messages.old_patient') }}
            </label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 col-md-6 old-patient d-none">
        <div class="appointment-form__input-block">
            <label for="patient_name" class="form-label">{{ __('messages.appointment.patient_name') }} :<span
                    class="required">*</span></label>
            {{ Form::text('patient_name', null, ['class' => 'form-control', 'id' => 'patientName', 'autocomplete' => 'off', 'required', 'disabled', 'placeholder' => __('messages.appointment.patient_name')]) }}
            {{ Form::hidden('patient_id', null, ['id' => 'patient']) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6 first-name-div">
        <div class="appointment-form__input-block">
            <label for="firstName" class="form-label">{{ __('messages.user.first_name') }} :<span
                    class="required">*</span></label>
            {{ Form::text('first_name', null, ['class' => 'form-control ', 'placeholder' => Lang::get('messages.web_appointment.enter_your_first_name'), 'autocomplete' => 'off', 'required', 'id' => 'frontAppointmentFirstName']) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6 last-name-div">
        <div class="appointment-form__input-block">
            <label for="lastName" class="form-label">{{ __('messages.user.last_name') }} :<span
                    class="required">*</span></label>
            {{ Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => Lang::get('messages.web_appointment.enter_your_last_name'), 'autocomplete' => 'off', 'required', 'id' => 'frontAppointmentLastName']) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6 gender-div">
        <div class="appointment-form__input-block">
            <label for="exampleFormControlInput1" class="form-label">{{ __('messages.user.gender') }} :<span
                    class="required">*</span></label>
            <div class="grp-radio d-flex align-items-center">
                <div class="form-check d-flex align-items-center mb-0">
                    {{ Form::radio('gender', '0', true, ['class' => 'form-check-input ms-3 radio_btn', 'id' => 'flexRadioSm']) }}
                    <label class="form-check-label ms-3" for="radioMale">{{ __('messages.user.male') }}</label>
                </div>
                <div class="form-check ms-4 d-flex align-items-center mb-0">
                    {{ Form::radio('gender', '1', false, ['class' => 'form-check-input radio_btn', 'id' => 'flexRadioSm']) }}
                    <label class="form-check-label ms-3" for="radioFemale">{{ __('messages.user.female') }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 patient-email-div">
        <div class="appointment-form__input-block">
            <label for="email" class="form-label">{{ __('messages.user.email') }} :
                <span class="required">*</span>
            </label>
            {{ Form::email('email', null, ['class' => 'form-control old-patient-email', 'placeholder' => Lang::get('messages.web_contact.enter_your_email'), 'autocomplete' => 'off', 'required']) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6 password-div">
        <div class="appointment-form__input-block">
            <label for="password" class="form-label">{{ __('messages.user.password') }} :<span
                    class="required">*</span></label>
            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => Lang::get('messages.web_appointment.enter_your_password'), 'required', 'min' => '6', 'max' => '10', 'id' => 'frontAppointmentPassword']) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6 confirm-password-div">
        <div class="appointment-form__input-block">
            <label for="confirmPassword" class="form-label">{{ __('messages.user.password_confirmation') }} :<span
                    class="required">*</span></label>
            {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => Lang::get('messages.web_appointment.enter_confirm_password'), 'required', 'min' => '6', 'max' => '10', 'id' => 'frontAppointmentConfirmPassword']) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="appointment-form__input-block">
            <label for="selectDepartment" class="form-label">{{ __('messages.appointment.doctor_department') }} :<span
                    class="required">*</span></label>

            {{ Form::select('department_id', $departments, null, ['id' => 'frontAppointmentDepartmentId', 'placeholder' => Lang::get('messages.web_appointment.select_department')]) }}
        </div>
        {{ Form::hidden('doctors', isset(session()->get('data')['doctorId']) ? session()->get('data')['doctorId'] : null, ['id' => 'doctor']) }}
        {{ Form::hidden('apdate', isset(session()->get('data')['appointmentDate']) ? session()->get('data')['appointmentDate'] : null, ['id' => 'appointmentDate']) }}
        {{--        <input type="hidden" id="doctor" value="{{$data['doctorId']}}"> --}}
        {{--        <input type="hidden" id="frontAppointmentDate" value="{{$data['appointmentDate']}}"> --}}
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="appointment-form__input-block">
            <label for="selectDoctor" class="form-label">{{ __('messages.appointment.doctor') }} :<span
                    class="required">*</span></label>
            {{ Form::select('doctor_id', $doctors, isset(session()->get('data')['doctorId']) ? session()->get('data')['doctorId'] : null, ['class' => 'selectized', 'autocomplete' => 'off', 'id' => 'frontAppointmentDoctorId', 'placeholder' => Lang::get('messages.web_appointment.select_doctor')]) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="appointment-form__input-block">
            <label for="date" class="form-label">{{ __('messages.investigation_report.date') }} :
                <span class="required">*</span>
            </label>
            {{ Form::text('opd_date', null, ['class' => 'form-control   ', 'autocomplete' => 'off', 'id' => 'frontAppointmentOPDDate', 'required','placeholder' => __('messages.investigation_report.date')]) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6 appointmentCharge d-none">
        <div class="form-group mb-5">
            {{ Form::label('appointment_charge', __('messages.appointment_charge') . ':', ['class' => 'form-label']) }}
            {{ Form::number('appointment_charge', null, ['class' => 'form-control','id' => 'webAppointmentCharge','placeholder' => 0,'readonly'=> 'readonly']) }}
        </div>
    </div>
    <div class="col-lg-4 col-md-6 web-appointment-payment d-none">
        <div class="form-group mb-5">
            <label for="payment_type" class="form-label">{{__('messages.ipd_payments.payment_mode')}} :
                <span class="required">*</span>
            </label>
            {{ Form::select('payment_type', getAppointmentPaymentTypes(),null, ['class' => 'selectized','id' => 'webAppointmentPayment','placeholder'=>__('messages.lunch_break.select_payment_mode')]) }}
        </div>
    </div>
    <div class="col-lg-12">
        <div class="appointment-form__input-block">
            <label for="description" class="form-label">{{ __('messages.appointment.description') }} :</label>
            {{ Form::textarea('problem', null, ['class' => 'form-control form-textarea', 'placeholder' => Lang::get('messages.web_appointment.enter_description'), 'autocomplete' => 'off', 'rows' => 4]) }}
        </div>
    </div>
    @if (count($customField) >= 0)
        @foreach ($customField as $field)
            @php
                $field_values = explode(',', $field['values']);
                $dateType = $field['field_type'] == 6 ? 'customFieldDate' : 'customFieldDateTime';
                $field_type = \App\Models\CustomField::FIELD_TYPE_ARR[$field['field_type']];
                $fieldName = 'field' . $field['id'];
                $fieldData = isset($appointment->custom_field[$fieldName]) ? $appointment->custom_field[$fieldName] : null;
            @endphp
            <div class="form-group col-sm-{{ $field['grid'] }} mb-5">
                {{ Form::label($field['field_name'], $field['field_name'] . ':', ['class' => $field['is_required'] == 1 ? 'form-label dynamic-field' : 'form-label']) }}
                @if ($field['is_required'] == 1)
                    <span class="required">*</span>
                @endif
                @if ($field_type == 'date' || $field_type == 'date & Time')
                    {{ Form::text($fieldName, $fieldData, ['id' => $dateType, 'class' => ($field['is_required'] == 1) ? 'form-control dynamic-field' : 'bg-white form-control', 'autocomplete' => 'off', 'placeholder' => $field['field_name'], $field['is_required'] == 1 ? 'required' : '']) }}
                @elseif ($field_type == 'toggle')
                    <div class="form-check form-switch form-check-custom">
                        <input class="form-check-input w-35px h-20px is-active {{ $field['is_required'] == 1 ? 'dynamic-field' : ''}}" name={{ $fieldName }}
                            type="checkbox" value="1" tabindex="8" {{ $fieldData == 0 ? '' : 'checked' }}>
                    </div>
                @elseif ($field_type == 'select')
                    {{ Form::select($fieldName, $field_values, isset($fieldData) ? $fieldData : null, ['class' => $field['is_required'] == 1 ? 'dynamic-field custom-field-select' : 'custom-field-select','id'=> 'customFieldSelect', 'placeholder' => $field['field_name']]) }}
                @elseif ($field_type == 'multiSelect')
                    {{ Form::select($fieldName . '[]', $field_values, isset($fieldData) ? $fieldData : null, ['class' => $field['is_required'] == 1 ? 'appointment-multi-select dynamic-field custom-field-multi-select' : 'appointment-multi-select custom-field-multi-select','id'=> 'customFieldMultiSelect','multiple', 'placeholder' => $field['field_name']]) }}
                @else
                    {{ Form::$field_type($fieldName, $fieldData, ['class' => $field['is_required'] == 1 ? 'form-control dynamic-field' : 'form-control', 'placeholder' => $field['field_name'], $field['is_required'] == 1 ? 'required' : '', 'rows' => $field_type == 'textarea' ? 4 : '']) }}
                @endif
            </div>
        @endforeach
    @endif
    <div align="left" class="form-group col-sm-12 appointment-slot">
        <div class="doctor-schedule" style="display: none">
            <i class="fas fa-calendar-alt"></i>
            <span class="day-name"></span>
            <span class="schedule-time"></span>
        </div>
        <strong class="error-message" style="display: none"></strong>
        <div class="slot-heading mb-4">
            <strong class="available-slot-heading" style="display: none">{{ __('messages.available_slots') }}:<span class="required">*</span></strong>
        </div>
        <div class="row">
            <div class="available-slot form-group col-sm-10">
            </div>
        </div>
        <?php
        $userName = request()->segment(2);
        $isEnabledGoogleCapcha = getSettingForReCaptcha($userName);
        ?>
        @if ($isEnabledGoogleCapcha == true)
            <div class="form-group col-xl-12">
                @if (config('app.recaptcha.key'))
                    <div class="g-recaptcha" id="appointment-g-recaptcha"
                        data-sitekey="{{ config('app.recaptcha.key') }}">
                    </div>
                @endif
            </div>
        @endif
        {{ Form::hidden('appointment-g-recaptcha', config('app.recaptcha.key'), ['id' => 'appointmentGRecaptcha']) }}
        <div class="color-information d-none" align="right" style="display: none">
            <span><i class="fa fa-circle fa-xs" aria-hidden="true"> </i> {{ __('messages.bed.not_available') }}</span>
        </div>
    </div>
    <div class="col-lg-12 text-center mt-4">
        <button type="submit" class="btn btn-primary custom-btn-lg"
            id="btnSave">{{ __('messages.common.save') }}</button>
    </div>
</div>
