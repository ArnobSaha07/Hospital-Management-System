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
        <p>A new live meeting has been created by <b>{{$created_by}}</b>.</p>
        <br>
        <p><b>Consultation Title:</b> {{$consultation_title}}</p>
        <p><b>Consultation Date:</b> {{$consultation_date}}</p>
        <p><b>Consultation Duration Time:</b> {{$consultation_duration_time}} Minutes</p>
        <br>
        <p style="text-align: center">To join the Zoom meeting, please click here:</p>
        <div style="margin-top:10px; margin-bottom:10px; display:block; text-align: center">
            <a href="{{ $zoom_redirect_url }}" target="_blank" style="padding: 0.563rem 1.563rem;  border: 1px solid transparent; background-color: #6571ff;  color: #fff;text-decoration:none; margin-bottom: 20px; border-radius: 5px">
                Start Now
            </a>
        </div>
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
