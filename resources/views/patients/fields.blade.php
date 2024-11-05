{{-- <div class="alert alert-danger display-none" id="customValidationErrorsBox"></div> --}}
<div class="row mb-5">
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('first_name', __('messages.user.first_name').':', ['class' => 'form-label']) }}<span
                    class="required"></span>
            {{ Form::text('first_name', null, ['class' => 'form-control', 'required', 'id' => 'firstName','tabindex' => '1', 'placeholder' => __('messages.user.first_name')]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('last_name', __('messages.user.last_name').':', ['class' => 'form-label']) }}<span
                    class="required"></span>
            {{ Form::text('last_name', null, ['class' => 'form-control', 'required', 'tabindex' => '2', 'placeholder' => __('messages.user.last_name')]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('email', __('messages.user.email').':', ['class' => 'form-label']) }}<span
                    class="required"></span>
            {{ Form::email('email', null, ['class' => 'form-control', 'required', 'tabindex' => '3', 'placeholder' => __('messages.user.email')]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('dob', __('messages.user.dob').':', ['class' => 'form-label']) }}
            {{ Form::text('dob', null, ['class' => (getLoggedInUser()->theme_mode) ? 'form-control bg-light patientBirthDate' : 'form-control bg-white patientBirthDate', 'autocomplete' => 'off', 'tabindex' => '4', 'placeholder' => __('messages.user.dob')]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mobile-overlapping  mb-5">
            {{ Form::label('phone', __('messages.user.phone') . ':', ['class' => 'form-label']) }}<span
                class="required"></span><br>
            {{ Form::tel('phone', null, ['class' => 'form-control phoneNumber', 'id' => 'phoneNumber', 'required', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'tabindex' => '5']) }}
            {{ Form::hidden('prefix_code', null, ['class' => 'prefix_code']) }}
            {{ Form::hidden('country_iso', null, ['class' => 'country_iso']) }}
            <span class="text-success valid-msg d-none fw-400 fs-small mt-2" id="valid-msg">✓ &nbsp;
                {{ __('messages.valid') }}</span>
            <span class="text-danger error-msg d-none fw-400 fs-small mt-2" id="error-msg"></span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-5">
            {{ Form::label('gender', __('messages.user.gender') . ':', ['class' => 'form-label']) }}<span
                class="required"></span> &nbsp;<br>
            <div class="d-flex align-items-center">
                <div class="form-check">
                    <label class="form-label" for="malePatient">{{ __('messages.user.male') }}</label>&nbsp;&nbsp;
                    {{ Form::radio('gender', '0', true, ['class' => 'form-check-input', 'tabindex' => '6', 'id' => 'malePatient']) }}
                    &nbsp;
                </div>
                <div class="form-check">
                    <label class="form-label" for="femalePatient">{{ __('messages.user.female') }}</label>
                    {{ Form::radio('gender', '1', false, ['class' => 'form-check-input', 'tabindex' => '7', 'id' => 'femalePatient']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-5">
            {{ Form::label('status', __('messages.common.status') . ':', ['class' => 'form-label']) }}
            <div class="form-check form-switch">
                <input class="form-check-input is-active" name="status" type="checkbox" value="1" tabindex="8"
                    checked>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('blood_group', __('messages.user.blood_group') . ':', ['class' => 'form-label']) }}
            {{ Form::select('blood_group', $bloodGroup, null, ['class' => 'form-select', 'id' => 'bloodGroupPatient', 'placeholder' => __('messages.user.select_blood_group'), 'data-control' => 'select2', 'tabindex' => '9']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-5">
            {{ Form::label('password', __('messages.user.password') . ':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::password('password', ['class' => 'form-control','required','min' => '6','max' => '10', 'tabindex' => '10', 'placeholder' => __('messages.user.password')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-5">
            {{ Form::label('password_confirmation', __('messages.user.password_confirmation') . ':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::password('password_confirmation', ['class' => 'form-control','required','min' => '6','max' => '10', 'tabindex' => '11', 'placeholder' => __('messages.user.password_confirmation')]) }}
        </div>
    </div>
    @if ($customField >= 0)
        @foreach ($customField as $field)
            @php
                $field_values = explode(',', $field['values']);
                $dateType = $field['field_type'] == 6 ? 'customFieldDate' : 'customFieldDateTime';
                $field_type = \App\Models\CustomField::FIELD_TYPE_ARR[$field['field_type']];
                $fieldName = 'field' . $field['id'];
            @endphp
            <div class="form-group col-sm-{{ $field['grid'] }} mb-5">
                {{ Form::label($field['field_name'], $field['field_name'] . ':', ['class' => $field['is_required'] == 1 ? 'form-label dynamic-field' : 'form-label']) }}
                @if ($field['is_required'] == 1)
                    <span class="required"></span>
                @endif
                @if ($field_type == 'date' || $field_type == 'date & Time')
                    {{ Form::text($fieldName, null, ['id' => $dateType, 'class' => getLoggedInUser()->thememode || $field['is_required'] == 1 ? 'bg-light form-control dynamic-field' : 'bg-white form-control', 'autocomplete' => 'off', 'placeholder' => $field['field_name'], $field['is_required'] == 1 ? 'required' : '']) }}
                @elseif ($field_type == 'toggle')
                    <div class="form-check form-switch form-check-custom">
                        <input class="form-check-input w-35px h-20px is-active {{ $field['is_required'] == 1 ? 'dynamic-field' : '' }}" name={{ $fieldName }}
                            type="checkbox" value="1" tabindex="8">
                    </div>
                @elseif ($field_type == 'select')
                    {{ Form::select($fieldName, $field_values, null, ['class' => $field['is_required'] == 1 ? 'form-select dynamic-field' : 'form-select', 'placeholder' => $field['field_name'], 'data-control' => 'select2', $field['is_required'] == 1 ? 'required' : '']) }}
                @elseif ($field_type == 'multiSelect')
                    {{ Form::select($fieldName . '[]', $field_values, 0, ['class' => $field['is_required'] == 1 ? 'form-select patient-multi-select dynamic-field' : 'form-select patient-multi-select','data-placeholder' => $field['field_name'], 'data-control' => 'select2', 'multiple' => true, $field['is_required'] == 1 ? 'required' : '']) }}
                @else
                    {{ Form::$field_type($fieldName, null, ['class' => $field['is_required'] == 1 ? 'form-control dynamic-field' : 'form-control', 'placeholder' => $field['field_name'], $field['is_required'] == 1 ? 'required' : '', 'rows' => $field_type == 'textarea' ? 4 : '']) }}
                @endif
            </div>
        @endforeach
    @endif
    <div class="form-group col-md-4 mb-5">
        <div class="row2">
            {{ Form::label('image', __('messages.profile.profile') . ':', ['class' => 'form-label']) }}
            <div class="d-block">
                <?php
                $style = 'style=';
                $background = 'background-image:';
                ?>

                <div class="image-picker">
                    <div class="image previewImage" id="PatientPreviewImage" {{ $style }}"{{ $background }}
                        url({{ asset('assets/img/avatar.png') }}">
                        <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                            data-placement="top" data-bs-original-title="{{ __('messages.profile.change_Profile') }}">
                            <label>
                                <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                <input type="file" id="" name="image" class="image-upload d-none"
                                    accept=".png, .jpg, .jpeg, .gif, .webp" />
                                <input type="hidden" name="avatar_remove" />
                            </label>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row mt-3 mb-5">
    <div class="col-md-12 mb-3">
        <h5>{{ __('messages.user.address_details') }}</h5>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('address1', __('messages.user.address1').':', ['class' => 'form-label']) }}
            {{ Form::text('address1', null, ['class' => 'form-control', 'placeholder' => __('messages.user.address1')]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('address2', __('messages.user.address2').':', ['class' => 'form-label']) }}
            {{ Form::text('address2', null, ['class' => 'form-control', 'placeholder' => __('messages.user.address2')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-5">
            {{ Form::label('city', __('messages.user.city').':', ['class' => 'form-label']) }}
            {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('messages.user.city')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-5">
            {{ Form::label('zip', __('messages.user.zip').':', ['class' => 'form-label']) }}
            {{ Form::text('zip', null, ['class' => 'form-control', 'maxlength' => '6','minlength' => '6', 'placeholder' => __('messages.user.zip')]) }}
        </div>
    </div>
</div>
<hr>
<div class="row mt-3 mb-5">
    <div class="col-md-12 mb-3">
        <h5>{{ __('messages.setting.social_details') }}</h5>
    </div>

    <!-- Facebook URL Field -->
    <div class="form-group col-sm-6 mb-5">
        {{ Form::label('facebook_url', __('messages.facebook_url').':', ['class' => 'form-label']) }}
        {{ Form::text('facebook_url', null, ['class' => 'form-control patientFacebookUrl', 'onkeypress' => 'return avoidSpace(event);', 'placeholder' => __('messages.facebook_url')]) }}
    </div>

    <!-- Twitter URL Field -->
    <div class="form-group col-sm-6 mb-5">
        {{ Form::label('twitter_url', __('messages.twitter_url').':', ['class' => 'form-label']) }}
        {{ Form::text('twitter_url', null, ['class' => 'form-control patientTwitterUrl', 'onkeypress' => 'return avoidSpace(event);', 'placeholder' => __('messages.twitter_url')]) }}
    </div>

    <!-- Instagram URL Field -->
    <div class="form-group col-sm-6 mb-5">
        {{ Form::label('instagram_url', __('messages.instagram_url').':', ['class' => 'form-label']) }}
        {{ Form::text('instagram_url', null, ['class' => 'form-control patientInstagramUrl', 'onkeypress' => 'return avoidSpace(event);', 'placeholder' => __('messages.instagram_url')]) }}
    </div>

    <!-- LinkedIn URL Field -->
    <div class="form-group col-sm-6 mb-5">
        {{ Form::label('linkedIn_url', __('messages.linkedIn_url').':', ['class' => 'form-label']) }}
        {{ Form::text('linkedIn_url', null, ['class' => 'form-control patientLinkedInUrl', 'onkeypress' => 'return avoidSpace(event);', 'placeholder' => __('messages.linkedIn_url')]) }}
    </div>

</div>
<div class="d-flex justify-content-end">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2', 'id' => 'btnSave']) }}
    <a href="{{ route('patients.index') }}" class="btn btn-secondary">{!! __('messages.common.cancel') !!}</a>
</div>
