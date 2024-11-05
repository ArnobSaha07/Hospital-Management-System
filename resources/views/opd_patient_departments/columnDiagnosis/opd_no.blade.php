@if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Doctor') || Auth::user()->hasRole('Receptionist'))
<a href="{{url('opds'.'/'.$row->opdPatientDepartment->id)}}"
    class="badge bg-light-info text-decoration-none">{{ $row->opdPatientDepartment->opd_number}}</a>
@else
    <div class="badge bg-light-info text-decoration-none">{{ $row->opdPatientDepartment->opd_number}}</div>
@endif
