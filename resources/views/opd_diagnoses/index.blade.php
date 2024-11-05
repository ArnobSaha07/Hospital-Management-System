@extends('layouts.app')
@section('title')
    {{ __('messages.ipd_diagnosis') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <livewire:diagnosis-table lazy/>
        </div>
    </div>
@endsection
