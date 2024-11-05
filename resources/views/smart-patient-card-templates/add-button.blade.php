@if (!Auth::user()->hasRole('Patient'))
    <a href="{{ route('patient-smart-card-templates.create') }}" class="btn btn-primary">
        {{ __('messages.lunch_break.new_smart_patient_card_template') }}
    </a>
@endif
