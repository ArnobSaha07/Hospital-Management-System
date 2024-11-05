<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomFieldRequest;
use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('custom_fields.index');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCustomFieldRequest $request)
    {
        $input = $request->all();
        $input['is_required'] = isset($input['is_required']) == 0 ? 0 : 1;
        CustomField::create($input);

        return $this->sendSuccess(__('messages.custom_field.custom_field').' '.__('messages.common.saved_successfully'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customField = CustomField::find($id);

        return $this->sendResponse($customField, 'data retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateCustomFieldRequest $request, $id)
    {
        $customField = CustomField::find($id);
        $input = $request->all();
        $input['is_required'] = isset($input['is_required']) == 0 ? 0 : 1;
        $customField->update($input);

        return $this->sendSuccess(__('messages.custom_field.custom_field').' '.__('messages.common.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        CustomField::where('id', $id)->delete();

        return $this->sendSuccess(__('messages.custom_field.custom_field').' '.__('messages.common.deleted_successfully'));
    }
}
