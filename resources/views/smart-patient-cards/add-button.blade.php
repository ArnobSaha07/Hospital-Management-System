@if(!getLoggedinPatient())
    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientCardModal">
        {{ __('messages.lunch_break.generate_smart_patient_card') }}
    </a>
@endif
