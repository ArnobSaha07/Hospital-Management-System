<div class="d-flex align-items-center">
    <a href="{{ route('pathology.test.pdf', $row->id) }}" title="<?php echo __('messages.new_change.print_pathology_test'); ?>" class="btn px-2 text-warning fs-3" target="_blank">
        <i class="fa fa-print"></i>
    </a>
    <a href="{{ route('pathology.test.edit', $row->id) }}" title="<?php echo __('messages.common.edit'); ?>" class="btn px-2 text-primary fs-3 ps-0">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <a href="#" title="<?php echo __('messages.common.delete'); ?>" data-id="{{ $row->id }}"
        class="delete-pathology-test-btn btn px-2 text-danger fs-3 ps-0">
        <i class="fa-solid fa-trash"></i>
    </a>
</div>
