@extends('layouts.app')
@section('title')
    {{ __('messages.manual_billing_payments') }}
@endsection

@section('content')
    @include('flash::message')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <livewire:manual-bill-payment-table lazy />
        </div>
    </div>
@endsection
