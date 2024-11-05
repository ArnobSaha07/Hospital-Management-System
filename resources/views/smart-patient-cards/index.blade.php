@extends('layouts.app')
@section('title')
    {{ __('messages.lunch_break.generate_smart_patient_cards') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            <livewire:patient-card-table lazy/>
        </div>
        @include('smart-patient-cards.add_modal')
        @include('smart-patient-cards.show_modal')
    </div>
@endsection
