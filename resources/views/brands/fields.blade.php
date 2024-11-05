<div class="row gx-10 mb-5">
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('name', __('messages.medicine.brand').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('name', null, ['id'=>'name','class' => 'form-control','required', 'placeholder' => __('messages.medicine.brand')]) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('phone', __('messages.user.phone').':', ['class' => 'form-label']) }}
            <br>
            {{ Form::tel('phone', null, ['class' => 'form-control phoneNumber','id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
            {{ Form::hidden('prefix_code',null,['class'=>'prefix_code']) }}
            {{ Form::hidden('country_iso', null, ['class' => 'country_iso']) }}
            <span class="text-success valid-msg d-none fw-400 fs-small mt-2" id="valid-msg">✓ &nbsp; {{__('messages.valid')}}</span>
            <span class="text-danger error-msg d-none fw-400 fs-small mt-2" id="error-msg"></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-5">
            {{ Form::label('email', __('messages.user.email').':', ['class' => 'form-label']) }}
            {{ Form::email('email', null, ['id'=>'email','class' => 'form-control', 'placeholder' => __('messages.user.email')]) }}
        </div>
    </div>
    <div class="d-flex justify-content-end">
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary', 'id' => 'btnSave']) }}
        <a href="{{ route('brands.index') }}"
           class="btn btn-secondary ms-2">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
