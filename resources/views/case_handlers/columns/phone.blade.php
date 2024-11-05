@if ($row->user->phone != '')
    <span>{{$row->user->region_code.$row->user->phone}}</span>
@else
    {{__('messages.common.n/a')}}
@endif
