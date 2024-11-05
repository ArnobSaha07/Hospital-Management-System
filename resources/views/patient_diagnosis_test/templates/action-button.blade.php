<div class="d-flex justify-content-center">
    <a href="{{route('patient.diagnosis.test.pdf',['patientDiagnosisTest' => $row->id])}}"
        title="<?php echo __('messages.patient_diagnosis_test.print_diagnosis_test') ?>"
          class="btn px-1 text-warning fs-3" target="_blank">
           <i class="fa fa-print"></i>
       </a>
    <a href="{{url('patient-diagnosis-test').'/'.$row->id.'/edit'}}" title="{{ __('messages.common.edit') }}"
       class="btn px-1 text-primary fs-3">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <a title="{{__('messages.common.delete')}}" href="javascript:void(0)" data-id="{{ $row->id }}"
       wire:key="{{$row->id}}"
       class="patient-diagnosys-test-delete-btn btn px-1 text-danger fs-3">
        <i class="fa-solid fa-trash"></i>
    </a>
</div>
