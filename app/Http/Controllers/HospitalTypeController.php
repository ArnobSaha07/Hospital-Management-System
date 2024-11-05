<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHospitalTypeRequest;
use App\Http\Requests\UpdateHospitalTypeRequest;
use App\Models\User;
use App\Repositories\HospitalTypeRepository;
use Flash;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HospitalTypeController extends AppBaseController
{
    /** @var HospitalTypeRepository */
    private $hospitalTypeRepository;

    public function __construct(HospitalTypeRepository $hospitalTypeRepo)
    {
        $this->hospitalTypeRepository = $hospitalTypeRepo;
    }

    /**
     * Display a listing of the HospitalType.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(): View
    {
        return view('hospital_type.index');
    }

    /**
     * Store a newly created HospitalType in storage.
     */
    public function store(CreateHospitalTypeRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->hospitalTypeRepository->create($input);

        return $this->sendSuccess(__('messages.hospitals_type').' '.__('messages.common.saved_successfully'));
    }

    /**
     * Display the specified HospitalType.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    //    public function show($id)
    //    {
    //        $hospitalType = $this->hospitalTypeRepository->find($id);
    //
    //        if (empty($hospitalType)) {
    //            Flash::error('Hospital Type not found');
    //
    ////            return redirect(route('hospitalTypes.index'));
    //        }
    //
    //        return view('hospital_types.show')->with('hospitalType', $hospitalType);
    //    }

    /**
     * Show the form for editing the specified HospitalType.
     */
    public function edit(int $id): JsonResponse
    {
        $hospitalType = $this->hospitalTypeRepository->find($id);

        if (empty($hospitalType)) {
            return $this->sendError(__('messages.flash.hospital_not_found'));
        }

        return $this->sendResponse($hospitalType, 'Hospital type retrieved successfully');
        //        return view('hospital_types.edit')->with('hospitalType', $hospitalType);
    }

    /**
     * Update the specified HospitalType in storage.
     */
    public function update(int $id, UpdateHospitalTypeRequest $request): JsonResponse
    {
        $hospitalType = $this->hospitalTypeRepository->find($id);

        if (empty($hospitalType)) {
            return $this->sendError(__('messages.flash.hospital_not_found'));
        }

        $this->hospitalTypeRepository->update($request->all(), $id);

        return $this->sendSuccess(__('messages.hospitals_type').' '.__('messages.common.updated_successfully'));
    }

    /**
     * Remove the specified HospitalType from storage.
     *
     *
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $hospitalType = $this->hospitalTypeRepository->find($id);

        if (empty($hospitalType)) {
            return $this->sendError(__('messages.flash.hospital_not_found'));
        }

        $models = [
            User::class,
        ];

        $hospitalExist = canDelete($models, 'hospital_type_id', $id);

        if ($hospitalExist) {
            return $this->sendError(__('messages.new_change.hospital_not_delete'));
        }

        $this->hospitalTypeRepository->delete($id);

        return $this->sendSuccess('Hospital Type deleted successfully.');
    }
}
