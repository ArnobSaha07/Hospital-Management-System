@php
    $medicineBill = App\Models\MedicineBill::whereModelType('App\Models\OpdPrescription')->whereModelId($row->id)->first();
@endphp
<div class="d-flex align-items-center justify-content-center">
    <a href="javascript:void(0)"
        class="btn px-1 text-info fs-3 viewOpdPrescription" data-id="{{$row->id}}">
        <i class="fas fa-eye"></i>
    </a>
    @if (! getLoggedinPatient())
        <a href="{{ route('opd.prescriptions.pdf',$row->id) }}"
            title="<?php echo __('messages.ipd_patient_prescription.print_prescription') ?>"
            class="btn px-1 text-warning fs-3" target="_blank">
            <i class="fa fa-print"></i>
        </a>
    @if(isset($medicineBill->payment_status) && $medicineBill->payment_status == false)
        <a href="javascript:void(0)" title="<?php echo __('messages.common.edit') ?>" data-id="{{$row->id}}"
            class="btn px-2 text-primary fs-3 py-2  editOpdPrescriptionBtn">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
    @endif
        <a  title="<?php echo __('messages.common.delete') ?>" data-id="{{$row->id}}"
            class="btn deleteOpdPrescriptionBtn px-2 text-danger fs-3">
            <i class="fa-solid fa-trash"></i>
        </a>
    @endif
</div>

