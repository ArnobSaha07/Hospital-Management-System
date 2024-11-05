@extends('layouts.app')
@section('title')
    {{ __('messages.account.accounts') }}
@endsection
@section('css')
    {{--    <link rel="stylesheet" href="{{ asset('assets/css/sub-header.css') }}"> --}}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            {{ Form::Hidden('accountCreateUrl', route('accounts.store'), ['id' => 'indexAccountCreateUrl']) }}
            {{ Form::Hidden('accountUrl', route('accounts.index'), ['class' => 'indexAccountUrl', 'id' => 'indexAccountUrl']) }}
            <livewire:account-table lazy />
            @include('accounts.add_modal')
            @include('accounts.edit_modal')
            {{--        @include('accounts.templates.templates') --}}
            @include('partials.modal.templates.templates')
            {{ Form::hidden('accountCreateURL', route('accounts.store'), ['id' => 'accountCreateURL']) }}
            {{ Form::hidden('account', __('messages.delete.account'), ['id' => 'account']) }}
            {{ Form::hidden('accountURL', route('accounts.index'), ['id' => 'accountURL']) }}
        </div>
    </div>
@endsection
{{--    <script src="{{ mix('assets/js/custom/new-edit-modal-form.js') }}"></script> --}}
{{--    <script src="{{ mix('assets/js/custom/delete.js') }}"></script> --}}
{{--    <script src="{{ mix('assets/js/custom/reset_models.js') }}"></script> --}}
{{-- let accountCreateUrl = "{{route('accounts.store')}}"; --}}
{{-- let accountUrl = "{{route('accounts.index')}}"; --}}
{{--    <script src="{{ mix('assets/js/accounts/accounts.js') }}"></script> --}}
