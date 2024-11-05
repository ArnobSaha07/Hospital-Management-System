@extends('layouts.app')
@section('title')
    {{ __('messages.dashboard.dashboard') }}
@endsection
@section('page_css')
    {{--    <link href="{{ mix('assets/css/dashboard.css') }}" rel="stylesheet" type="text/css"/> --}}
@endsection
@section('content')
    {{--    {{Form::hidden('super_admin_dashboard',true,['class'=>'super-admin-dashboard'])}} --}}
    <div class="container-fluid">
        <span class="text-decoration-none super-admin-dashboard d-none"></span>
        <livewire:dashboard lazy />
    </div>
@endsection
{{--    <script src="{{ asset('assets/js/plugins/daterangepicker.js') }}"></script> --}}
{{--    <script src="{{ mix('assets/js/super_admin/dashboard/dashboard.js') }}"></script> --}}
