<div class="d-flex align-items-center justify-content-center">
    <a href={{ route('investigation-reports.show', $row->id) }} title="<?php echo __('messages.common.view'); ?>"
        class="btn text-info px-1  fs-3 ps-0">
        <i class="fas fa-eye"></i>
    </a>
    @if (!getLoggedinPatient())
        <a href="{{ route('investigation-reports.edit', $row->id) }}" title="<?php echo __('messages.common.edit'); ?>"
            class="btn px-1 text-primary fs-3">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
        <a href="#" title="<?php echo __('messages.common.delete'); ?>" data-id="{{ $row->id }}"
            class="delete-in-report-btn btn px-1 pe-2 text-danger fs-3">
            <i class="fa-solid fa-trash"></i>
        </a>
    @endif

</div>
