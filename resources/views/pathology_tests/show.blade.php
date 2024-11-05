@extends('layouts.app')
@section('title')
    {{ __('messages.pathology_test.pathology_test_details')}}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-7">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end d-flex flex-wrap gap-3 justify-content-end mt-4 mt-md-0">
                <a href="{{ route('pathology.test.pdf', $pathologyTest->id) }}"
                    target="_blank"
                    class="btn btn-success ">{{ __('messages.new_change.print_pathology_test') }}
                 </a>
                <a href="{{ route('pathology.test.edit',['pathologyTest' => $pathologyTest->id])}}"
                   class="btn btn-primary  edit-btn">{{ __('messages.common.edit') }}</a>
                <a href="{{ url()->previous() }}"
                   class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        @include('flash::message')
        <div class="d-flex flex-column">
            <div class="card">
                <div class="card-body">
                    @include('pathology_tests.show_fields')
                </div>
            </div>
        </div>
    </div>
@endsection
