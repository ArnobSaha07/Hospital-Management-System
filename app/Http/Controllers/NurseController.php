<?php

namespace App\Http\Controllers;

use App\Exports\NurseExport;
use App\Http\Requests\CreateNurseRequest;
use App\Http\Requests\UpdateNurseRequest;
use App\Models\EmployeePayroll;
use App\Models\Nurse;
use App\Repositories\NurseRepository;
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

class NurseController extends AppBaseController
{
    /** @var NurseRepository */
    private $nurseRepository;

    public function __construct(NurseRepository $nurseRepo)
    {
        $this->nurseRepository = $nurseRepo;
    }

    /**
     * Display a listing of the Nurse.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = Nurse::STATUS_ARR;

        return view('nurses.index', $data);
    }

    /**
     * Show the form for creating a new Nurse.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $bloodGroup = getBloodGroups();

        return view('nurses.create', compact('bloodGroup'));
    }

    /**
     * Store a newly created Nurse in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateNurseRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = ! isset($input['status']) ? 0 : 1;
        $input['region_code'] = regionCode($input['prefix_code']);
        $nurse = $this->nurseRepository->store($input);

        Flash::success(__('messages.flash.nurse_saved'));

        return redirect(route('nurses.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Nurse $nurse)
    {
        if (! canAccessRecord(Nurse::class, $nurse->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $payrolls = $nurse->payrolls;

        return view('nurses.show', compact('nurse', 'payrolls'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Nurse $nurse)
    {
        if (! canAccessRecord(Nurse::class, $nurse->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $user = $nurse->user;
        $bloodGroup = getBloodGroups();

        return view('nurses.edit', compact('user', 'nurse', 'bloodGroup'));
    }

    /**
     * Update the specified Nurse in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Nurse $nurse, UpdateNurseRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['region_code'] = regionCode($input['prefix_code']);
        $user = $this->nurseRepository->update($nurse, $input);

        Flash::success(__('messages.flash.nurse_updated'));

        return redirect(route('nurses.index'));
    }

    /**
     * Remove the specified Nurse from storage.
     *
     * @return RedirectResponse|Redirector|JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Nurse $nurse)
    {
        if (! canAccessRecord(Nurse::class, $nurse->id)) {
            return $this->sendError(__('messages.flash.nurse_not_found'));
        }

        $empPayRollResult = canDeletePayroll(EmployeePayroll::class, 'owner_id', $nurse->id, $nurse->user->owner_type);
        if ($empPayRollResult) {
            return $this->sendError(__('messages.flash.nurse_cant_deleted'));
        }
        $nurse->user()->delete();
        $nurse->address()->delete();
        $nurse->delete();

        return $this->sendSuccess(__('messages.flash.nurse_deleted'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        $nurse = Nurse::findOrFail($id);
        $status = ! $nurse->user->status;
        $nurse->user()->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function nurseExport(): BinaryFileResponse
    {
        $response = Excel::download(new NurseExport, 'nurses-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
