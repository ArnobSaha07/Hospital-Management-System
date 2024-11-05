<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountantRequest;
use App\Http\Requests\UpdateAccountantRequest;
use App\Models\Accountant;
use App\Models\EmployeePayroll;
use App\Repositories\AccountantRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AccountantController extends AppBaseController
{
    /** @var AccountantRepository */
    private $accountantRepository;

    public function __construct(AccountantRepository $accountantRepo)
    {
        $this->accountantRepository = $accountantRepo;
    }

    /**
     * Display a listing of the Accountant.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = Accountant::STATUS_ARR;

        return view('accountants.index', $data);
    }

    /**
     * Show the form for creating a new Accountant.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $bloodGroup = getBloodGroups();

        return view('accountants.create', compact('bloodGroup'));
    }

    /**
     * Store a newly created Accountant in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateAccountantRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['region_code'] = regionCode($input['prefix_code']);
        $input['status'] = isset($input['status']) ? 1 : 0;

        $accountant = $this->accountantRepository->store($input);

        Flash::success(__('messages.flash.accountant_save'));

        return redirect(route('accountants.index'));
    }

    /**
     * Display the specified Accountant.
     *
     * @return Factory|View
     */
    public function show(Accountant $accountant)
    {
        if (! canAccessRecord(Accountant::class, $accountant->id)) {
            return Redirect::back();
        }

        $payrolls = $accountant->payrolls;

        return view('accountants.show', compact('accountant', 'payrolls'));
    }

    /**
     * Show the form for editing the specified Accountant.
     *
     * @return Factory|View
     */
    public function edit(Accountant $accountant)
    {
        if (! canAccessRecord(Accountant::class, $accountant->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $user = $accountant->user;
        $bloodGroup = getBloodGroups();

        return view('accountants.edit', compact('user', 'accountant', 'bloodGroup'));
    }

    /**
     * Update the specified Accountant in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Accountant $accountant, UpdateAccountantRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['region_code'] = regionCode($input['prefix_code']);
        $accountant = $this->accountantRepository->update($accountant, $input);

        Flash::success(__('messages.flash.accountant_update'));

        return redirect(route('accountants.index'));
    }

    /**
     * Remove the specified Accountant from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Accountant $accountant): JsonResponse
    {
        if (! canAccessRecord(Accountant::class, $accountant->id)) {
            return $this->sendError(__('messages.flash.accountant_cant_delete'));
        }

        $empPayRollResult = canDeletePayroll(EmployeePayroll::class, 'owner_id', $accountant->id,
            $accountant->user->owner_type);
        if ($empPayRollResult) {
            return $this->sendError(__('messages.flash.accountant_cant_delete'));
        }
        $accountant->user()->delete();
        $accountant->address()->delete();
        $accountant->delete();

        return $this->sendSuccess(__('messages.flash.accountant_delete'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        $accountant = Accountant::findOrFail($id);
        $status = ! $accountant->user->status;
        $accountant->user()->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }
}
