@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot
    @php($superAdmin = App\Models\User::orderBy('created_at')->first())
    <p><b>Hello {{ $superAdmin->first_name.' '.$superAdmin->last_name }},</b></p>
    <p>This is a enquiry notification from <b>{{ $data['first_name'].' '.$data['last_name']  }}</b></p>
    <p>Phone: {{$data['phone']}}</p>
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
