<?php

namespace App\Http\Controllers;

use App\Exports\AmbulanceCallExport;
use App\Http\Requests\CreateAmbulanceCallRequest;
use App\Http\Requests\UpdateAmbulanceCallRequest;
use App\Models\Ambulance;
use App\Models\AmbulanceCall;
use App\Repositories\AmbulanceCallRepository;
use App\Repositories\AmbulanceRepository;
use App\Repositories\PatientRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AmbulanceCallController extends AppBaseController
{
    /** @var AmbulanceCallRepository */
    private $ambulanceCallRepository;

    /** @var AmbulanceRepository */
    private $ambulanceRepository;

    /** @var PatientRepository */
    private $patientRepository;

    public function __construct(
        AmbulanceCallRepository $ambulanceCallRepo,
        AmbulanceRepository $ambulanceRepo,
        PatientRepository $patientRepo
    ) {
        $this->ambulanceCallRepository = $ambulanceCallRepo;
        $this->ambulanceRepository = $ambulanceRepo;
        $this->patientRepository = $patientRepo;
    }

    /**
     * Display a listing of the Payment.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('ambulance_calls.index');
    }

    /**
     * Show the form for creating a new AmbulanceCall.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $ambulances = $this->ambulanceRepository->getAmbulances();
        $patients = $this->patientRepository->getPatients();

        return view('ambulance_calls.create', compact('ambulances', 'patients'));
    }

    /**
     * Store a newly created AmbulanceCall in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateAmbulanceCallRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);

        $ambulanceCall = $this->ambulanceCallRepository->create($input);
        Ambulance::where('id', $input['ambulance_id'])->update(['is_available' => false]);

        Flash::success(__('messages.flash.ambulance_call_saved'));

        return redirect(route('ambulance-calls.index'));
    }

    /**
     * Display the specified AmbulanceCall.
     *
     * @return Factory|View
     */
    public function show(AmbulanceCall $ambulanceCall)
    {
        if (! canAccessRecord(AmbulanceCall::class, $ambulanceCall->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('ambulance_calls.show')->with('ambulanceCall', $ambulanceCall);
    }

    /**
     * Show the form for editing the specified Payment.
     *
     * @return Factory|View
     */
    public function edit(AmbulanceCall $ambulanceCall)
    {
        if (! canAccessRecord(AmbulanceCall::class, $ambulanceCall->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $ambulances = $this->ambulanceRepository->getAmbulances();
        $patients = $this->patientRepository->getPatients();
        $ambulance = Ambulance::whereId($ambulanceCall->ambulance_id)->first()->vehicle_model;
        $ambulances->put($ambulanceCall->ambulance_id, $ambulance);

        return view('ambulance_calls.edit', compact('ambulances', 'patients', 'ambulanceCall'));
    }

    /**
     * Update the specified AmbulanceCall in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(AmbulanceCall $ambulanceCall, UpdateAmbulanceCallRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);
        $ambulanceCall = $this->ambulanceCallRepository->update($input, $ambulanceCall);

        Flash::success(__('messages.flash.ambulance_call_updated'));

        return redirect(route('ambulance-calls.index'));
    }

    /**
     * Remove the specified Payment from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(AmbulanceCall $ambulanceCall): JsonResponse
    {
        if (! canAccessRecord(AmbulanceCall::class, $ambulanceCall->id)) {
            return $this->sendError(__('messages.flash.ambulance_call_not_found'));
        }

        $this->ambulanceCallRepository->delete($ambulanceCall->id);

        return $this->sendSuccess(__('messages.flash.ambulance_call_deleted'));
    }

    public function getDriverName(Request $request): JsonResponse
    {
        if (empty($request->get('id'))) {
            return $this->sendError(__('messages.flash.driver_not_found'));
        }

        $driverName = Ambulance::whereId($request->id)->get()->pluck('driver_name');

        return $this->sendResponse($driverName, __('messages.flash.driver_not_found'));
    }

    public function ambulanceCallExport(): BinaryFileResponse
    {
        $response = Excel::download(new AmbulanceCallExport, 'ambulance-calls-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
