<div class="d-flex align-items-center justify-content-center">
    <a title="{{ __('messages.expense.download')}}" class="btn px-1 fs-3 ps-0 text-primary" href="{{url('smart-patient-card-download').'/'.$row->id}}" target="_blank">
        <i class="fa fa-download"></i>
    </a>
    <a href="javascript:void(0)" title="<?php echo __('messages.common.view') ?>" data-id="{{$row->id}}"
        class="btn px-1 text-info fs-3 show-patient-smart-card">
        <i class="fas fa-eye"></i>
    </a>
    @if(!getLoggedinPatient())
        <a href="javascript:void(0)" title="<?php echo __('messages.common.delete') ?>" data-id="{{$row->id}}"
            class="btn delete-smart-patient-card-btn px-2 text-danger fs-3">
            <i class="fa-solid fa-trash"></i>
        </a>
    @endif
</div>
