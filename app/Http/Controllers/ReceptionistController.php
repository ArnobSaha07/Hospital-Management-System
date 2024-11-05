<?php

namespace App\Http\Controllers;

use App\Exports\ReceptionistExport;
use App\Http\Requests\CreateReceptionistRequest;
use App\Http\Requests\UpdateReceptionistRequest;
use App\Models\EmployeePayroll;
use App\Models\Receptionist;
use App\Repositories\ReceptionistRepository;
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

class ReceptionistController extends AppBaseController
{
    /** @var ReceptionistRepository */
    private $receptionistRepository;

    public function __construct(ReceptionistRepository $receptionistRepo)
    {
        $this->receptionistRepository = $receptionistRepo;
    }

    /**
     * Display a listing of the Receptionist.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = Receptionist::STATUS_ARR;

        return view('receptionists.index', $data);
    }

    /**
     * Show the form for creating a new Receptionist.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $bloodGroup = getBloodGroups();

        return view('receptionists.create', compact('bloodGroup'));
    }

    /**
     * Store a newly created Receptionist in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateReceptionistRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['region_code'] = regionCode($input['prefix_code']);

        $receptionist = $this->receptionistRepository->store($input);

        Flash::success(__('messages.flash.receptionist_saved'));

        return redirect(route('receptionists.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Receptionist $receptionist)
    {
        if (! canAccessRecord(Receptionist::class, $receptionist->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $payrolls = $receptionist->payrolls;

        return view('receptionists.show', compact('receptionist', 'payrolls'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Receptionist $receptionist)
    {
        if (! canAccessRecord(Receptionist::class, $receptionist->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $user = $receptionist->user;
        $bloodGroup = getBloodGroups();

        return view('receptionists.edit', compact('receptionist', 'user', 'bloodGroup'));
    }

    /**
     * Update the specified Receptionist in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Receptionist $receptionist, UpdateReceptionistRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['region_code'] = regionCode($input['prefix_code']);

        $receptionist = $this->receptionistRepository->update($receptionist, $input);

        Flash::success(__('messages.flash.receptionist_updated'));

        return redirect(route('receptionists.index'));
    }

    /**
     * Remove the specified Receptionist from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Receptionist $receptionist): JsonResponse
    {
        if (! canAccessRecord(Receptionist::class, $receptionist->id)) {
            return $this->sendError(__('messages.flash.receptionist_not_found'));
        }

        $empPayRollResult = canDeletePayroll(EmployeePayroll::class, 'owner_id', $receptionist->id,
            $receptionist->user->owner_type);
        if ($empPayRollResult) {
            return $this->sendError(__('messages.flash.receptionist_cant_deleted'));
        }
        $receptionist->user()->delete();
        $receptionist->address()->delete();
        $receptionist->delete();

        return $this->sendSuccess(__('messages.flash.receptionist_deleted'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        if (! canAccessRecord(Receptionist::class, $id)) {
            return $this->sendError(__('messages.flash.receptionist_not_found'));
        }

        $receptionist = Receptionist::findOrFail($id);
        $status = ! $receptionist->user->status;
        $receptionist->user()->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function receptionistExport(): BinaryFileResponse
    {
        $response = Excel::download(new ReceptionistExport, 'receptionists-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
