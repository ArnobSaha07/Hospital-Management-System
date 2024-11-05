@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot
    <p><b>Dear {{ $patient_name }},</b></p>
    <p>Hope you are having a great day!!</p>
    <p>This is a friendly reminder that your appointment with <b>Dr.{{$doctor_name}}</b> is within next one hour.</p>
    <p>Your Problem: {{$problem}}</p>
    <p>Appointment Time: {{ \Carbon\Carbon::parse($appointment_date)->translatedFormat('jS M, Y g:i A') }}</p>
    <p>You may contact us with your suitable time for your Doctor Appointment & we are here to assist you 24/7.</p>
    <br>
    <p>Thanks & Regards,</p>
    <p>{{ getSuperAdminAppName() }}</p>
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <h6>© {{ date('Y') }} {{ getSuperAdminAppName() }}.</h6>
        @endcomponent
    @endslot
@endcomponent
