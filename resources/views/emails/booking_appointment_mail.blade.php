@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot
    {{-- Body --}}
    <div>
        <h2>Hello!</h2>
        <p>A new appointment has been booked by <b>{{$patient_type}} patient</b>.</p>
        <br>
        <p><b>Booking Date:</b> {{$booking_date}}</p>
        <p><b>Patient Name:</b> {{$patient_name}}</p>
        <p><b>Patient Email:</b> {{$patient_email}}</p>
        <p><b>Doctor Name:</b> {{$doctor_name}}</p>
        <p><b>Doctor Department:</b> {{$doctor_department}}</p>
        <p><b>Doctor Email:</b> {{$doctor_email}}</p>
        <br>
        <p>Thanks & Regards,</p>
        <p>{{ getSuperAdminAppName() }}</p>
    </div>
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <h6>© {{ date('Y') }} {{ getSuperAdminAppName() }}.</h6>
        @endcomponent
    @endslot
@endcomponent
