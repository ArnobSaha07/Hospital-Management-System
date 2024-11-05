<div class="mt-3">
    @if ($row->bedType)
        <a href="{{ route('bed-types.show',$row->bedType->id) }}" class="text-decoration-none">{{ $row->bedType->title }}</a>
    @else
        <div class="text-decoration-none">{{ __('messages.common.n/a') }}</div>
    @endif
</div>

