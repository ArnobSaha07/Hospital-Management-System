@extends('landing.layouts.app')
@section('title')
    {{ __('messages.hospitals') }}
@endsection
@section('page_css')
    {{--    <link href="{{mix('landing_front/css/home.css')}}" rel="stylesheet" type="text/css"> --}}
@endsection
@section('content')
    <div class="home-page">
        <section class="hero-section pt-120 bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-lg-start text-center mb-lg-0 mb-md-5 mb-sm-4 mb-3">
                        <div class="hero-content">
                            <h1 class="mb-0">
                                {{ __('messages.hospitals') }}
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-content-lg-start justify-content-center mb-lg-0 pb-lg-4">
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('landing-home') }}">{{ __('messages.web_home.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item text-cyan fs-18" aria-current="page">
                                        {{ __('messages.hospitals') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-lg-6 text-lg-end text-center">
                        <img src="{{ asset('landing_front/images/about-hero-img.png') }}" alt="HMS-Sass"
                            class="img-fluid" loading="lazy"/>
                    </div>
                </div>
                <div class="col-lg-6 text-lg-end text-center">
                    <img src="{{ asset('landing_front/images/about-hero-img.png') }}" alt="HMS-Sass"
                        class="img-fluid" loading="lazy"/>
                </div>
            </div>
        </div>
    </section>

    <section class="our-hospitals-section py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <div class="section-heading">
                        <h2>{{ __('messages.our_hospitals') }}</h2>
                    </div>
                </div>
            </div>
        @livewire('hospitals-listing')
        </div>
    </section>
    @include('landing.home.subscribe_section')
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            var selectize = $("#hospitalType").selectize();

            selectize[0].selectize.on("change", function (value) {
                $("#hospitalType").selectize();
                Livewire.dispatch("hospitalType", {type:value});
            });

            $(document).on("click",".reset-filter", function () {
                selectize[0].selectize.clear();
                selectize[0].selectize.setValue("0");
            });
        });
    </script>
    {{--    <script>var toastData = ''</script> --}}
@endsection
