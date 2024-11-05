<?php
    $defaultHospitalAdmin = \App\Models\UserTenant::whereUserId($row->id)->exists();
?>
<div class="d-flex align-items-center">
    @if(!$defaultHospitalAdmin)
    <a href="{{route('users.edit', $row->id)}}" title="<?php echo __('messages.common.edit') ?>"
       class="btn px-2 text-primary fs-3 ps-0 py-2">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    @if(getLoggedInUser()->hasRole('Admin'))
        <a href="javascript:void(0)" title="<?php echo __('messages.common.delete') ?>" data-id="{{$row->id}}"
           class="btn delete-user-btn px-2 text-danger fs-3 py-2">
            <i class="fa-solid fa-trash"></i>
        </a>
    @endif
@endif
</div>
