<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHospitalRequest;
use App\Http\Requests\UpdateHospitalRequest;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\HospitalRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class HospitalController extends AppBaseController
{
    /** @var HospitalRepository */
    private $hospitalRepository;

    public function __construct(HospitalRepository $hospitalRepo)
    {
        $this->hospitalRepository = $hospitalRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View
    {
        $data = $this->hospitalRepository->getSyncList();

        return view('super_admin.users.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Application|RedirectResponse|Redirector
     *
     * @throws \Throwable
     */
    public function store(CreateHospitalRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['region_code'] = regionCode($input['prefix_code']);
        $this->hospitalRepository->store($input);

        Flash::success(__('messages.flash.hospital_saved'));

        return redirect(route('super.admin.hospitals.index'));
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|View
     */
    public function show($id)
    {
        $user = User::find($id);
        if (empty($user) || ! $user->hasRole('Admin')) {
            Flash::error(__('messages.flash.hospital_not_found'));

            return redirect(route('super.admin.hospitals.index'));
        }

        $users = $this->hospitalRepository->getUserData($id);

        return view('super_admin.users.show', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $hospital = User::find($id);

        if (empty($hospital) || ! $hospital->hasRole('Admin')) {
            Flash::error(__('messages.flash.hospital_not_found'));

            return redirect(route('super.admin.hospitals.index'));
        }

        $data = $this->hospitalRepository->getSyncList();

        return view('super_admin.users.edit', compact('hospital'))->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(UpdateHospitalRequest $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $input = $request->all();
        $input['region_code'] = regionCode($input['prefix_code']);
        $this->hospitalRepository->updateHospital($input, $user);

        Flash::success(__('messages.flash.hospital_update'));

        return redirect(route('super.admin.hospitals.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $hospital = User::find($id);

        if (empty($hospital) || ! $hospital->hasRole('Admin')) {
            return $this->sendError(__('messages.flash.hospital_not_found'));
        }

        $this->hospitalRepository->deleteHospital($id);

        return $this->sendSuccess(__('messages.flash.user_deleted'));
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function billingIndex(Request $request)
    {
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function transactionIndex(Request $request)
    {
    }

    public function billingModal($id): JsonResponse
    {
        $subscription = Subscription::with('subscriptionPlan', 'transactions')->where('transaction_id', $id)->get();

        return $this->sendResponse($subscription, __('messages.flash.subscription_retrieved'));
    }
}
