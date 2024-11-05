@extends('layouts.app')
@section('title')
    {{ __('messages.custom_field.custom_field') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            {{ Form::hidden('add-custom-fields', route('custom-fields.store'), ['class' => 'addCustomFieldURL']) }}
            {{Form::hidden('addCustomFieldUrl',url('custom-fields'),['id'=>'indexAddCustomFieldURL'])}}
            {{ Form::hidden('AddCustomFields', __('messages.custom_field.custom_field'), ['id' => 'customField']) }}
            <livewire:custom-field-table lazy/>
        </div>
        @include('custom_fields.custom_field_modal')
        @include('custom_fields.edit_custom_field_modal')
    </div>
@endsection
