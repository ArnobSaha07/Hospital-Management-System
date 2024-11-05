
    @if(!empty($row->user->phone))
        {{$row->user->region_code.$row->user->phone}}
    @else
        {{ __('messages.common.n/a') }}
    @endif

