<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePathologyUnitRequest;
use App\Http\Requests\UpdatePathologyUnitRequest;
use App\Models\PathologyParameter;
use Illuminate\Http\Request;
use App\Models\PathologyTest;
use App\Models\PathologyUnit;
use App\Repositories\PathologyUnitRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PathologyUnitController extends AppBaseController
{

    /** @var PathologyUnitRepository */
    private $pathologyUnitRepository;

    public function __construct(PathologyUnitRepository $pathologyUnitRepo)
    {
        // $this->middleware('check_menu_access');
        $this->pathologyUnitRepository = $pathologyUnitRepo;
    }
    /**
     * Display a listing of the PathologyCategory.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('pathology_units.index');
    }

    /**
     * Store a newly created PathologyCategory in storage.
     */
    public function store(CreatePathologyUnitRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->pathologyUnitRepository->create($input);

        return $this->sendSuccess(__('messages.new_change.pathology_unit').' '.__('messages.common.saved_successfully'));
    }

    /**
     * Show the form for editing the specified PathologyCategory.
     */
    public function edit(PathologyUnit $pathologyUnit): JsonResponse
    {
        if (! canAccessRecord(PathologyUnit::class, $pathologyUnit->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($pathologyUnit, __('Pathology Unit retrieved successfully.'));
    }

    /**
     * Update the specified PathologyCategory in storage.
     */
    public function update(PathologyUnit $pathologyUnit, UpdatePathologyUnitRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->pathologyUnitRepository->update($input, $pathologyUnit->id);

        return $this->sendSuccess(__('messages.new_change.pathology_unit').' '.__('messages.common.updated_successfully'));
    }

    /**
     * Remove the specified PathologyCategory from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(PathologyUnit $pathologyUnit): JsonResponse
    {
        if (! canAccessRecord(PathologyUnit::class, $pathologyUnit->id)) {
            return $this->sendError(__('messages.new_change.pathology_unit_not_found'));
        }

        $pathologyParameterModels = [
            PathologyParameter::class,
        ];
        $result = canDelete($pathologyParameterModels, 'unit_id', $pathologyUnit->id);
        if ($result) {
            return $this->sendError(__('messages.new_change.pathology_unit_cant_deleted'));
        }

        $pathologyUnit->delete();

        return $this->sendSuccess(__('messages.new_change.pathology_unit').' '.__('messages.common.deleted_successfully'));
    }
}
