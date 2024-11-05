@if ($row->status === 1)
    <a href="javascript:void(0)" data-id="{{$row->id}}" class="btn btn-primary btn-sm user-impersonate">
        {{ __('messages.impersonate') }}
    </a>
@else
    <span class="text text-center">{{ __('messages.common.n/a') }}</span>
@endif
