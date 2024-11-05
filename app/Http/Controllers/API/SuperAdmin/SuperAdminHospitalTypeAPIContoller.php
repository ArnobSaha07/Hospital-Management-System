<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\AppBaseController;
use App\Models\HospitalType;

class SuperAdminHospitalTypeAPIContoller extends AppBaseController
{
    public function index()
    {
        $hospitalTypes = HospitalType::orderBy('id', 'desc')->get();
        $data = [];
        foreach ($hospitalTypes as $hospitalType) {
            $data[] = $hospitalType->prepareHospitalType();
        }
        return $this->sendResponse($data, 'Hospital Type Retrieved Successfully');

    }

    public function show($id): \Illuminate\Http\JsonResponse
    {

        $hospitalTypes = HospitalType::find($id);

        return $this->sendResponse($hospitalTypes, 'Hospital Type Retrieved Successfully');
    }
}
