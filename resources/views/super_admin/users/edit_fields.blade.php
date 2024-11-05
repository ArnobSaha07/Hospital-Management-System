<div class="row mb-5 pt-5">
    <div class="col-md-4">

        <div class="mb-5">
            {{ Form::label('hospital_name', __('messages.hospitals_list.hospital_name').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('hospital_name', null, ['class' => 'form-control', 'required', 'tabindex' => '1', 'placeholder' =>__('messages.user.enter_hospital_name'), 'pattern'  => '^[a-zA-Z0-9 ]+$',  'title' => 'Hospital Name Not Allowed Special Character']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-5">
            {{ Form::label('username', __('messages.user.hospital_slug').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('username', null, ['class' => 'form-control', 'required', 'tabindex' => '1', 'placeholder' => __('messages.user.enter_hospital_slug'), 'readonly', 'min' => 12, 'maxlength' => 12]) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-5">
            {{ Form::label('hospital_type', __('messages.hospitals_type') . (':'), ['class' => 'form-label']) }}
            {{ Form::select('hospital_type_id', $hospitalType, $hospital->hospital_type_id ? $hospital->hospital_type_id : null, ['class' => 'form-select', 'id' => 'hospitalTypeId', 'placeholder' => __('messages.hospitals_type'), 'data-control' => 'select2']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-5">
            {{ Form::label('email',__('messages.user.email').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::email('email', null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.user.enter_email'), 'tabindex' => '3']) }}
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-5">
            {{ Form::label('email',__('messages.user.city').':', ['class' => 'form-label']) }}
            {{ Form::text('city', null, ['class' => 'form-control', 'tabindex' => '3', 'placeholder' => __('messages.user.city')]) }}
        </div>
    </div>
    <div class="col-md-4">
        <div>
            {{ Form::label('Phone',__('messages.visitor.phone').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::tel('phone',isset($hospital->phone) ? $hospital->region_code.$hospital->phone : null, ['class' => 'form-control iti phoneNumber', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")', 'tabindex' => '5', 'required']) }}
            {{ Form::hidden('prefix_code',null,['class' => 'prefix_code']) }}
            <span class="text-success d-none fw-400 fs-small mt-2 valid-msg" id="valid-msg">✓ &nbsp; {{__('messages.valid')}}</span>
            <span class="text-danger d-none fw-400 fs-small mt-2 error-msg" id="error-msg"></span>
        </div>
    </div>
</div>
<div class="d-flex float-end">
    {!! Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2','id' => 'hospitalEditBtnSave']) !!}
    <a href="{!! route('super.admin.hospitals.index') !!}"
       class="btn btn-secondary">{{ __('messages.common.cancel') }}</a>
</div>
