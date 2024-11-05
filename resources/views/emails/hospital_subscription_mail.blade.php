@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot
    {{-- Body --}}
    <div>
        <h2>Hello {{ $user_name }},</h2>
        <p>You have successfully upgrade,</p>
        <br>
        <p><b>Plan Name:</b> {{$plan_name}}</p>
        <p><b>Start Date:</b> {{$start_date}}</p>
        <p><b>End Date:</b> {{$end_date}}</p>
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
