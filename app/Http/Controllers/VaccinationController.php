<?php

namespace App\Http\Controllers;

use App\Exports\VaccinationExport;
use App\Http\Requests\CreateVaccinationRequest;
use App\Http\Requests\UpdateVaccinationRequest;
use App\Models\VaccinatedPatients;
use App\Models\Vaccination;
use App\Repositories\VaccinationRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VaccinationController extends AppBaseController
{
    /**
     * @var VaccinationRepository
     */
    private $vaccinationRepository;

    public function __construct(VaccinationRepository $vaccinationRepository)
    {
        $this->middleware('check_menu_access');
        $this->vaccinationRepository = $vaccinationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     *
     * @throws \Exception
     */
    public function index(Request $request): View
    {
        return view('vaccinations.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateVaccinationRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            $this->vaccinationRepository->create($input);

            return $this->sendSuccess(__('messages.flash.vaccination_saved'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vaccination $vaccination): JsonResponse
    {
        if (! canAccessRecord(Vaccination::class, $vaccination->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($vaccination, __('messages.flash.vaccination_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVaccinationRequest $request, Vaccination $vaccination): JsonResponse
    {
        try {
            $input = $request->all();
            $this->vaccinationRepository->update($input, $vaccination->id);

            return $this->sendSuccess(__('messages.flash.vaccination_updated'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vaccination $vaccination): JsonResponse
    {
        if (! canAccessRecord(Vaccination::class, $vaccination->id)) {
            return $this->sendError(__('messages.flash.vaccination_not_found'));
        }

        try {
            $vaccinatedModels = [
                VaccinatedPatients::class,
            ];

            $result = canDelete($vaccinatedModels, 'vaccination_id', $vaccination->id);

            if ($result) {
                return $this->sendError(__('messages.flash.vaccination_cant_deleted'));
            }

            $vaccination->delete();

            return $this->sendSuccess(__('messages.flash.vaccination_deleted'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function vaccinationsExport(): BinaryFileResponse
    {
        $response = Excel::download(new VaccinationExport, 'vaccinations-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
