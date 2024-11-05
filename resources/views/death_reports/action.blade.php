<div class="d-flex align-items-center justify-content-center">
    <a href={{ route('death-reports.show', $row->id) }} title="<?php echo __('messages.common.view'); ?>" class="btn text-info px-1  fs-3 ps-0">
        <i class="fas fa-eye"></i>
    </a>
    @if (!getLoggedinPatient())
        <a href="javascript:void(0)" title="<?php echo __('messages.common.edit'); ?>" data-id="{{ $row->id }}"
            class="btn px-1 text-primary fs-3 edit-death-report-btn">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
        <a href="#" title="<?php echo __('messages.common.delete'); ?>" data-id="{{ $row->id }}"
            class="btn delete-death-report-btn px-1 fs-3 pe-2 text-danger">
            <i class="fa-solid fa-trash"></i>
        </a>
    @endif
</div>
