@extends('layouts.app')
@section('title')
    {{ __('messages.lunch_break.new_smart_patient_card_template') }}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-7">
            <h1 class="mb-0">@yield('title')</h1>
            <a href="{{ route('patient-smart-card-templates.index') }}"
               class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <div class="col-12">
                    @include('layouts.errors')
                    @include('flash::message')
                </div>
            </div>
            <div class="card">
                <div class="card-body p-12">
                    {{ Form::open(['route' => 'patient-smart-card-templates.store', 'files' => 'true']) }}
                    @include('smart-patient-card-templates.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
