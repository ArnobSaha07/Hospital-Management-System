<?php

namespace App\Http\Controllers;

use App\Exports\BedExport;
use App\Http\Requests\CreateBedRequest;
use App\Http\Requests\CreateBulkBedRequest;
use App\Http\Requests\UpdateBedRequest;
use App\Models\Bed;
use App\Models\BedAssign;
use App\Models\IpdPatientDepartment;
use App\Repositories\BedRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BedController extends AppBaseController
{
    /** @var BedRepository */
    private $bedRepository;

    public function __construct(BedRepository $bedRepo)
    {
        $this->bedRepository = $bedRepo;
    }

    /**
     * Display a listing of the Bed.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = Bed::STATUS_ARR;

        $bedTypes = $this->bedRepository->getBedTypes();

        return view('beds.index', compact('bedTypes'), $data);
    }

    /**
     * Store a newly created Bed in storage.
     */
    public function store(CreateBedRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['charge'] = removeCommaFromNumbers($input['charge']);

        $bed = $this->bedRepository->store($input);

        return $this->sendSuccess(__('messages.flash.bed_saved'));
    }

    /**
     * Display the specified Bed.
     *
     * @return Factory|View
     */
    public function show(Bed $bed)
    {
        if (!canAccessRecord(Bed::class, $bed->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $bedAssigns = $bed->bedAssigns()->orderBy('created_at', 'desc')->get();
        $bedTypes = $this->bedRepository->getBedTypes();

        return view('beds.show', compact('bed', 'bedAssigns', 'bedTypes'));
    }

    /**
     * Show the form for editing the specified Bed.
     */
    public function edit(Bed $bed): JsonResponse
    {
        if (!canAccessRecord(Bed::class, $bed->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($bed, __('messages.flash.bed_retrieved'));
    }

    /**
     * Update the specified Bed in storage.
     */
    public function update(Bed $bed, UpdateBedRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['charge'] = removeCommaFromNumbers($input['charge']);

        $bed = $this->bedRepository->update($input, $bed->id);

        return $this->sendSuccess(__('messages.flash.bed_updated'));
    }

    /**
     * Remove the specified Bed from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Bed $bed): JsonResponse
    {
        if (!canAccessRecord(Bed::class, $bed->id)) {
            return $this->sendError(__('messages.flash.bed_not_found'));
        }

        $bedModel = [
            BedAssign::class, IpdPatientDepartment::class,
        ];
        $result = canDelete($bedModel, 'bed_id', $bed->id);
        if ($result) {
            return $this->sendError(__('messages.flash.bed_cant_deleted'));
        }
        $this->bedRepository->delete($bed->id);

        return $this->sendSuccess(__('messages.flash.bed_deleted'));
    }

    public function activeDeActiveStatus(int $id): JsonResponse
    {
        $bed = Bed::findOrFail($id);
        $bed->status = !$bed->status;
        $bed->update(['status' => $bed->status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    /**
     * @return Factory|View
     */
    public function createBulkBeds(): View
    {
        $bedTypes = $this->bedRepository->getBedTypes();
        $associateBedTypes = $this->bedRepository->getAssociateBedsList();

        return view('beds.create_bulk_beds', compact('bedTypes', 'associateBedTypes'));
    }

    public function storeBulkBeds(CreateBulkBedRequest $request): JsonResponse
    {
        $input = $request->all();
        $bulkBeds = $this->bedRepository->storeBulkBeds($input);

        return $this->sendResponse($bulkBeds, __('messages.flash.beds_saved'));
    }

    public function bedExport(): BinaryFileResponse
    {
        $response = Excel::download(new BedExport, 'beds-' . time() . '.xlsx');

        ob_end_clean();

        return $response;
    }
}
