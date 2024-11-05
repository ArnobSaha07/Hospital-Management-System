@if($row->user->phone !== null)
    {{$row->user->region_code.$row->user->phone}}
@else
    {{ __('messages.common.n/a')}}
@endif
