@extends('layouts.app')
@section('title')
{{ __('messages.hospitals_type') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            {{ Form::hidden('hospitalTypeCreateUrl', route('super.admin.hospitals.type.store'), ['class' => 'hospitalTypeCreateUrl']) }}
            {{ Form::hidden('hospitalTypeEditUrl', url('hospital-type'),['id' => 'hospitalTypeEditUrl']) }}
            <livewire:hospital-type-table lazy/>
            @include('hospital_type.create-modal')
            @include('hospital_type.edit-modal')
        </div>
    </div>
@endsection
