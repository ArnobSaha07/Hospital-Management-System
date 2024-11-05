@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot
    {{-- Body --}}
    <p><b> {{ __('messages.new_change.hello') }} {{ __('messages.new_change.dr.') }}{{$doctor_name}},</b></p>
    <p>{{ __('messages.new_change.reminder') }} {{$patient_name}} {{ __('messages.new_change.within_one_hour') }}</p>
    <p>{{ __('messages.new_change.patient_problem') }}: {{$problem}}</p>
    <p>{{ __('messages.new_change.appointment_time') }}: {{ \Carbon\Carbon::parse($appointment_date)->translatedFormat('jS M, Y g:i A') }}</p>
    <br>
    <p>{{ __('messages.new_change.thanks_regards') }},</p>
    <p>{{ getSuperAdminAppName() }}</p>
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <h6>© {{ date('Y') }} {{ getSuperAdminAppName() }}.</h6>
        @endcomponent
    @endslot
@endcomponent
