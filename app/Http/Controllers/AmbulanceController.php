<?php

namespace App\Http\Controllers;

use App\Exports\AmbulanceExport;
use App\Http\Requests\CreateAmbulanceRequest;
use App\Http\Requests\UpdateAmbulanceRequest;
use App\Models\Ambulance;
use App\Models\AmbulanceCall;
use App\Repositories\AmbulanceRepository;
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

class AmbulanceController extends AppBaseController
{
    /** @var AmbulanceRepository */
    private $ambulanceRepository;

    public function __construct(AmbulanceRepository $ambulanceRepo)
    {
        $this->ambulanceRepository = $ambulanceRepo;
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
        $data['statusArr'] = Ambulance::STATUS_ARR;

        return view('ambulances.index', $data);
    }

    /**
     * Show the form for creating a new Ambulance.
     */
    public function create(): View
    {
        $type = Ambulance::$vehicleType;

        return view('ambulances.create', compact('type'));
    }

    /**
     * Store a newly created Ambulance in storage.
     */
    public function store(CreateAmbulanceRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['is_available'] = isset($input['is_available']) ? 1 : 0;
        $input['driver_contact'] = preparePhoneNumber($input, 'driver_contact');

        $this->ambulanceRepository->create($input);
        $this->ambulanceRepository->createNotification();

        Flash::success(__('messages.flash.ambulance_saved'));

        return redirect(route('ambulances.index'));
    }

    /**
     * Display the specified Ambulance.
     *
     * @return Factory|View
     */
    public function show(int $id)
    {
        if (! canAccessRecord(Ambulance::class, $id)) {
            return Redirect::back();
        }

        $ambulance = Ambulance::findOrFail($id);
        $type = Ambulance::$vehicleType;

        return view('ambulances.show', compact('ambulance', 'type'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Ambulance $ambulance)
    {
        if (! canAccessRecord(Ambulance::class, $ambulance->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $type = Ambulance::$vehicleType;

        return view('ambulances.edit', compact('ambulance', 'type'));
    }

    /**
     * Update the specified Payment in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Ambulance $ambulance, UpdateAmbulanceRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['is_available'] = isset($input['is_available']) ? 1 : 0;
        $input['driver_contact'] = preparePhoneNumber($input, 'driver_contact');

        $ambulance = $this->ambulanceRepository->update($input, $ambulance->id);

        Flash::success(__('messages.flash.ambulance_update'));

        return redirect(route('ambulances.index'));
    }

    /**
     * Remove the specified Ambulance from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Ambulance $ambulance): JsonResponse
    {
        if (! canAccessRecord(Ambulance::class, $ambulance->id)) {
            return $this->sendError(__('messages.flash.ambulance_not_found'));
        }

        //        $this->ambulanceRepository->delete($ambulance->id);
        $ambulanceCallModel = [AmbulanceCall::class];
        $result = canDelete($ambulanceCallModel, 'ambulance_id', $ambulance->id);
        if ($result) {
            return $this->sendError(__('messages.flash.ambulance_cant_delete'));
        }

        $ambulance->delete($ambulance->id);

        return $this->sendSuccess(__('messages.flash.ambulance_delete'));
    }

    public function isAvailableAmbulance(int $id): JsonResponse
    {
        $ambulance = Ambulance::findOrFail($id);
        $ambulance->is_available = ! $ambulance->is_available;
        $ambulance->update(['is_available' => $ambulance->is_available]);

        return $this->sendSuccess(__('messages.flash.ambulance_update'));
    }

    public function ambulanceExport(): BinaryFileResponse
    {
        $response = Excel::download(new AmbulanceExport, 'ambulances-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
