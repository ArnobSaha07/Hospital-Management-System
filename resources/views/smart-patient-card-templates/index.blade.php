@extends('layouts.app')
@section('title')
    {{ __('messages.lunch_break.smart_patient_card_template') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            <livewire:smart-patient-card-table lazy />
        </div>
    </div>
@endsection
