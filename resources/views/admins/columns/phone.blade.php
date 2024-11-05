@if($row->phone)
    {{  $row->region_code.$row->phone }}
@else
    {{ __('messages.common.n/a') }}
@endif
