<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePathologyParameterRequest;
use App\Http\Requests\UpdatePathologyParameterRequest;
use App\Models\PathologyParameter;
use App\Models\PathologyParameterItem;
use App\Models\PathologyUnit;
use App\Repositories\PathologyParameterRepository;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PathologyParameterController extends AppBaseController
{
    /** @var PathologyParameterRepository */
    private $pathologyParameterRepository;

    public function __construct(PathologyParameterRepository $pathologyParameterRepo)
    {
        // $this->middleware('check_menu_access');
        $this->pathologyParameterRepository = $pathologyParameterRepo;
    }

    /**
     * Display a listing of the PathologyCategory.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        $unit = $this->pathologyParameterRepository->getPathologyUnitData();

        return view('pathology_parameter.index', compact('unit'));
    }

    /**
     * Store a newly created PathologyCategory in storage.
     */
    public function store(CreatePathologyParameterRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->pathologyParameterRepository->create($input);

        return $this->sendSuccess(__('messages.new_change.pathology_parameter').' '.__('messages.common.saved_successfully'));
    }

    /**
     * Show the form for editing the specified PathologyCategory.
     */
    public function edit(PathologyParameter $pathologyParameter): JsonResponse
    {
        if (! canAccessRecord(PathologyParameter::class, $pathologyParameter->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($pathologyParameter, __('messages.flash.pathology_category_retrieved'));
    }

    /**
     * Update the specified PathologyCategory in storage.
     */
    public function update(PathologyParameter $pathologyParameter, UpdatePathologyParameterRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->pathologyParameterRepository->update($input, $pathologyParameter->id);

        return $this->sendSuccess(__('messages.new_change.pathology_parameter').' '.__('messages.common.updated_successfully'));
    }

    /**
     * Remove the specified PathologyCategory from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(PathologyParameter $pathologyParameter): JsonResponse
    {
        if (! canAccessRecord(PathologyParameter::class, $pathologyParameter->id)) {
            return $this->sendError(__('messages.new_change.pathology_parameter_not_found'));
        }

        $pathologyParameterModels = [
            PathologyParameterItem::class,
        ];
        $result = canDelete($pathologyParameterModels, 'parameter_id', $pathologyParameter->id);
        if ($result) {
            return $this->sendError(__('messages.new_change.pathology_parameter_cant_deleted'));
        }

        $pathologyParameter->delete();

        return $this->sendSuccess(__('messages.new_change.pathology_parameter').' '.__('messages.common.deleted_successfully'));
    }
}
