<div class="d-flex align-items-center justify-content-center">
    <a href={{ route('birth-reports.show', $row->id) }} title="<?php echo __('messages.common.view'); ?>" class="btn text-info px-1  fs-3 ps-0">
        <i class="fas fa-eye"></i>
    </a>
    @if (!getLoggedinPatient())
        <a href="javascript:void(0)" title="<?php echo __('messages.common.edit'); ?>" data-id="{{ $row->id }}"
            class="btn text-primary fs-3 px-1 edit-birth-report-btn">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
        <a href="#" title="<?php echo __('messages.common.delete'); ?>" data-id="{{ $row->id }}"
            class="btn delete-birth-report-btn fs-3 text-danger pe-2 px-1">
            <i class="fa-solid fa-trash"></i>
        </a>
    @endif
</div>
