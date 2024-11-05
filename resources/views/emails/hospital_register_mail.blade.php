@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot
    {{-- Body --}}
    <div>
        <h2>{{ __('messages.new_change.hello') }}!</h2>
        <p>{{ __('messages.new_change.new_hospital_registerd') }}</p>
        <br>
        <p><b>{{ __('messages.hospitals_list.hospital_name') }}:</b> {{$hospital_name}}</p>
        <p><b>{{ __('messages.setting.hospital_email') }}:</b> {{$hospital_email}}</p>
        <p><b>{{ __('messages.new_change.hospital_contact') }}:</b> {{$hospital_phone}}</p>
        <br>
        <p>{{ __('messages.new_change.thanks_regards') }},</p>
        <p>{{ getSuperAdminAppName() }}</p>
    </div>
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <h6>© {{ date('Y') }} {{ getSuperAdminAppName() }}.</h6>
        @endcomponent
    @endslot
@endcomponent
