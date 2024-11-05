<div class="d-flex align-items-center mt-2">
    @if($row->caseFromBedAssign)
        <a href="{{ url('patient-cases').'/'.$row->caseFromBedAssign->id }}" class="badge bg-light-info text-decoration-none">{{ $row->case_id }}</a>
    @else
        <span class="badge bg-light-danger">{{__('messages.common.n/a')}}</span>
    @endif
</div>
