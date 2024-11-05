<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateHospitalRequest;
use App\Models\User;
use App\Repositories\HospitalRepository;
use App\Http\Requests\UpdateHospitalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuperAdminHospitalAPIController extends AppBaseController
{
    /** @var HospitalRepository */
    private $hospitalRepository;

    public function __construct(HospitalRepository $hospitalRepo)
    {
        $this->hospitalRepository = $hospitalRepo;
    }

    public function createHospital(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'hospital_name' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'city' => 'required',
            'hospital_type_id' => 'required',
            'region_code' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $input = $request->all();
        $this->hospitalRepository->store($input);

        return response()->json([
            'data' => $input,
            'message' => 'Hospital created successfully',
        ], JsonResponse::HTTP_CREATED);
    }

    public function hospitalRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hospital_name' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'region_code' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $input = $request->all();
        $input['region_code'] = regionCode($input['region_code']);
        $data = $this->hospitalRepository->store($input);

        return response()->json([
            'success' => $data,
            'message' => 'Hospital created successfully',
        ]);

    }

    public function showHospital()
    {
        $hospitals = User::with(['department', 'media', 'hospitalType'])
                    ->where('department_id', '=', User::USER_ADMIN)
                    ->whereNotNull('username')
                    ->whereNotNull('hospital_name')
                    ->orderBy('id','desc')->get();

        $data = [];

        foreach ($hospitals as $hospital) {
            $data[] = $hospital->PreperHospitalData();
        }

        return $this->sendResponse($data, 'Hospital retrieved successfully');
    }

    public function editHospital(int $id)
    {
        $hospital = User::find($id);
        if (empty($hospital) || ! $hospital->hasRole('Admin')) {
            return $this->sendSuccess('Hospital not found');
        }

        $data = $this->hospitalRepository->getSyncList();

        return  $this->sendSuccess($hospital, 'Hospital retrieve successfully.');
    }

    public function updateHospital(Request $request,  $id)
    {
        try {
            $user = User::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendError('Hospital not found.');
        }

        $input = $request->all();
        $input['region_code'] = regionCode($input['region_code']);
        $data =  $this->hospitalRepository->updateHospital($input, $user);

        return response()->json(['success' => $data, 'message' => 'Hospital updated successfully.',
        ]);
    }

    public function deleteHospital(int $id): JsonResponse
    {
        $hospital = User::find($id);

        if (empty($hospital) || ! $hospital->hasRole('Admin')) {
            return $this->sendSuccess('Hospital not found');
        }

        $this->hospitalRepository->deleteHospital($id);

        return $this->sendSuccess('Hospital deleted successfully');
    }

    public function filter(Request $request): JsonResponse
    {
        $status = $request->get('status');
        $data = [];
        $hospitalsQuery = User::with(['department', 'media', 'hospitalType'])
            ->where('department_id', '=', User::USER_ADMIN)
            ->whereNotNull('username')
            ->whereNotNull('hospital_name');

        if($status == 'all'){
            $hospitals = $hospitalsQuery->orderBy('id', 'desc')->get();

            foreach ($hospitals as $hospital) {
                $data[] = $hospital->PreperHospitalData();
            }
            return $this->sendResponse($data, 'Hospital retrieved successfully');
        }elseif($status == 'deactive') {
            $hospitals = $hospitalsQuery->whereStatus(0)->orderBy('id','desc')->get();

            foreach ($hospitals as $hospital) {
                $data[] = $hospital->PreperHospitalData();
            }
            return $this->sendResponse($data, 'Hospital retrieved successfully');
        }elseif($status == 'active') {
            $hospitals =$hospitalsQuery->whereStatus(1)->orderBy('id','desc')->get();

            foreach ($hospitals as $hospital) {
                $data[] = $hospital->PreperHospitalData();
            }
            return $this->sendResponse($data, 'Hospital retrieved successfully');
        }else{
            return $this->sendResponse($data, 'Hospital not found');
        }
    }
}
