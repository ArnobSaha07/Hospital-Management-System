@if ($row->gender == 1)
<span class="badge bg-light-primary">{{__('messages.user.male')}}</span>
@else
<span class="badge bg-light-success">{{__('messages.user.female')}}</span>
@endif
