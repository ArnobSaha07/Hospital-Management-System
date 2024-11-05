@extends('layouts.app')
@section('title')
    {{ __('messages.dashboard.dashboard') }}
@endsection
@section('page_css')
    {{--    <link href="{{ mix('assets/css/dashboard.css') }}" rel="stylesheet" type="text/css"/> --}}
    {{--        <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.css') }}"> --}}
    {{--        <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}"> --}}
@endsection
@section('content')
    <?php
    $currencySymbol = getCurrencySymbol();
    ?>
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <livewire:admin-dashboard lazy />

            {{--             Income & Expense Chart --}}
            {{--                        <div class="row"> --}}
            {{--                            <div class="col-lg-12"> --}}
            {{--                                <div class="card"> --}}
            {{--                                    <div class="card-body"> --}}
            {{--                                        <div class="row justify-content-between"> --}}
            {{--                                            <div class="col-sm-6 col-md-6 col-lg-6 pt-2"> --}}
            {{--                                                <h5>{{ __('messages.dashboard.income_and_expense_report') }}</h5> --}}
            {{--                                            </div> --}}
            {{--                                            <div class="col-md-3"> --}}
            {{--                                                <div id="time_range" class="time_range d-flex"> --}}
            {{--                                                    <i class="far fa-calendar-alt" --}}
            {{--                                                       aria-hidden="true"></i>&nbsp;&nbsp;<span></span> --}}
            {{--                                                    <b class="caret"></b> --}}
            {{--                                                </div> --}}
            {{--                                            </div> --}}
            {{--                                        </div> --}}
            {{--                                        <div class="table-responsive-sm"> --}}
            {{--                                            <div class="pt-2"> --}}
            {{--                                                <canvas id="daily-work-report" class="mh-400px"></canvas> --}}
            {{--                                            </div> --}}
            {{--                                        </div> --}}
            {{--                                    </div> --}}
            {{--                                </div> --}}
            {{--                            </div> --}}
            {{--                        </div> --}}
        </div>
        {{ Form::hidden('incomeExpenseReportUrl', route('income-expense-report'), ['id' => 'dashboardIncomeExpenseReportUrl', 'class' => 'incomeExpenseReportUrl']) }}
        {{ Form::hidden('currentCurrencyName', $currencySymbol, ['id' => 'dashboardCurrentCurrencyName', 'class' => 'currentCurrencyName']) }}
        {{ Form::hidden('income_and_expense_reports', __('messages.dashboard.income_and_expense_reports'), ['id' => 'dashboardIncome_and_expense_reports', 'class' => 'income_and_expense_reports']) }}
        {{ Form::hidden('defaultAvatarImageUrl', asset('assets/img/avatar.png'), ['id' => 'dashboardDefaultAvatarImageUrl', 'class' => 'defaultAvatarImageUrl']) }}
        {{ Form::hidden('noticeBoardUrl', url('notice-boards'), ['id' => 'dashboardNoticeBoardUrl', 'class' => 'noticeBoardUrl']) }}
    </div>
@endsection
{{--    <script src="{{ asset('assets/js/chart.min.js') }}"></script> --}}
{{--    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script> --}}
{{-- let incomeExpenseReportUrl = "{{route('income-expense-report')}}"; --}}
{{-- let currentCurrencyName = "{{ getCurrencySymbol()}}"; --}}
{{-- let curencies = JSON.parse('@json($data['currency'])'); --}}
{{-- let income_and_expense_reports = "{{ __('messages.dashboard.income_and_expense_reports') }}"; --}}
{{-- let defaultAvatarImageUrl = "{{ asset('assets/img/avatar.png') }}"; --}}
{{--    <script src="{{mix('assets/js/dashboard/dashboard.js')}}"></script> --}}
{{--    <script src="{{mix('assets/js/custom/input_price_format.js')}}"></script> --}}
