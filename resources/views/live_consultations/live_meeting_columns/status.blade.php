<?php
$colours = ['warning', 'danger', 'success',];
$adminRole = getLoggedInUser()->hasRole('Admin') ? true : false;
$doctorRole = getLoggedInUser()->hasRole('Doctor') ? true : false;
?>

@if ($adminRole || $doctorRole)
    @if($row->status == 0)
    <div class="w-150px d-flex align-items-center">
        <span class="slot-color-dot badge bg-{{$colours[$row->status]}} badge-circle me-2"></span>
        <select class="form-select change-meeting-status" data-id="{{ $row->id }}" data-control="select2">
            <option value="0" {{ $row->status == 0 ? 'selected' : '' }}{{ ($row->status == 1 || $row->status == 2)  ? 'disabled' : '' }}>
                Awaited
            </option>
            <option value="1" {{$row->status == 1 ? 'selected' : ''}}{{ ($row->status == 2)  ? 'disabled' : '' }}>
                Cancelled
            </option>
            <option value="2" {{ $row->status == 2 ? 'selected' : '' }}{{ ($row->status  == 1)  ? 'disabled' : '' }}>
                Finished
            </option>
        </select>
    </div>
    @elseif ($row->status == 1)
        <span class="badge bg-light-danger ms-2 fs-8 py-1 px-3"> {{ $row->status_text }}</span>
    @elseif ($row->status == 2)
        <span class="badge bg-light-success ms-2 fs-8 py-1 px-3"> {{ $row->status_text }}</span>
    @endif
@else
    <div class="mt-3">
        {{$row->status_text}}
    </div>
@endif
