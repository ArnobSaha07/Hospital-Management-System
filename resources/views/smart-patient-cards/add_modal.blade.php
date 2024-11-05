<div id="addPatientCardModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ __('messages.lunch_break.generate_smart_patient_card') }}</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{ Form::open(['id' => 'addPatientCardForm']) }}
            <div class="modal-body">
                <div class="alert alert-danger d-none hide" id="patientCardErrorsBox"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-5">
                            {{ Form::label('template_id', __('messages.lunch_break.template_name') . ':', ['class' => 'form-label']) }}
                            <span class="required"></span>
                            {{ Form::select('template_id', $cardTemplates, null, ['class' => 'form-select template-name', 'required', 'placeholder' => __('messages.lunch_break.select_template'), 'required', 'id' => 'cardTemplateID']) }}
                        </div>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label">{{ __('messages.common.select_type') }}:
                        <span class="required"></span></label>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-5">

                            {{ Form::radio('patient', 'all', true, ['class' => 'form-check-input', 'id' => 'allPatient']) }}
                            &nbsp;<label class="form-label"for="allPatient">{{ __('messages.lunch_break.for_all_patient') }}</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-5">
                            {{ Form::radio('patient', 'remaining', false, ['class' => 'form-check-input', 'id' => 'remainingPatient']) }}
                            &nbsp;<label class="form-label"for="remainingPatient">{{ __('messages.lunch_break.remaining_patient') }}</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-5">
                            {{ Form::radio('patient', 'one', false, ['class' => 'form-check-input one-patient', 'id' => 'onePatient']) }}
                            &nbsp;<label class="form-label"for="onePatient">{{ __('messages.lunch_break.only_one_patient') }}</label>
                        </div>
                    </div>
                    <div class="col-12  d-none customisePatient">
                        <div class="form-group mb-5">
                            {{ Form::label('patient_id', __('messages.document.select_patient') . ':', ['class' => 'form-label']) }}
                            <span class="required"></span>
                            {{ Form::select('patient_id', $patients, null, ['class' => 'form-select patient-id', 'placeholder' => __('messages.document.select_patient'), 'id' => 'cardTemplatePatientID']) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-0">
                    {{ Form::button(__('messages.common.save'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'patientCardSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" aria-label="Close" class="btn btn-secondary ms-2"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
