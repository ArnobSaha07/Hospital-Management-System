@if($row->doctorUser->phone !== null)
    {{$row->doctorUser->region_code.$row->doctorUser->phone}}
@else
    {{ __('messages.common.n/a')}}
@endif
