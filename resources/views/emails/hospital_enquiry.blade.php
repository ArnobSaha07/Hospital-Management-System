@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot

    <p><b>Hello {{getUser()->full_name}},</b></p>
    <p>This is a enquiry notification from <b>{{ $data['full_name'] }}</b></p>
    <p>Purpose: {{$data['purpose']}}</p>
    <p>Phone: {{$data['contact_no']}}</p>
    <p>Email: {{$data['email']}}</p>
    <p>Message: {{$data['message']}}</p>
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
