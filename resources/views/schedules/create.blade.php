@extends('layouts.app')
@section('title')
    {{ __('messages.schedule.new') }}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-7">
            <h1 class="mb-0">@yield('title')</h1>
            <a href="{{ route('schedules.index') }}"
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
                    @if ($data['hospitalSchedule'] == null)
                        <div class="py-5">
                            <div class="d-flex align-items-center rounded py-5 px-5 bg-light-danger">
                                <span class="svg-icon svg-icon-3x svg-icon-danger me-5">                                                                                                             </span>
                                <div class="text-gray-700 text-danger fw-bold fs-6">{{ __('You cannot set your schedule until your hospital adds it') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-body p-12">
                    {{Form::hidden('hospitalSchedule',json_encode($data['hospitalSchedule']),['id'=>'createHospitalSchedule','class'=>'hospitalSchedule'])}}
                    {{ Form::open(['route' => 'schedules.store', 'files' => 'true', 'id' => 'createScheduleForm','class'=>'scheduleForm']) }}
                    @include('schedules.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
{{--let hospitalSchedule = @JSON($data['hospitalSchedule']);--}}
{{--    <script src="{{ mix('assets/js/schedules/create-edit.js') }}"></script>--}}
