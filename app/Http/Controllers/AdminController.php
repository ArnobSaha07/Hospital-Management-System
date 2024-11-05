<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use App\Repositories\AdminRepository;
use Flash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends AppBaseController
{
    /** @var AdminRepository */
    private $adminRepository;

    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepository = $adminRepo;
    }

    /**
     * Display a listing of the Admin.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request): View
    {
        return view('admins.index');
    }

    /**
     * Show the form for creating a new Admin.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(): View
    {
        return view('admins.create');
    }

    /**
     * Store a newly created Admin in storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateAdminRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['region_code'] = regionCode($input['prefix_code']);
        $this->adminRepository->store($input);

        Flash::success(__('messages.admin_user.admin_saved_successfully'));

        return redirect(route('admins.index'));
    }

    /**
     * Display the specified Admin.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show(int $id)
    {
        $admin = $this->adminRepository->find($id);

        if (empty($admin) || ! $admin->hasRole('Super Admin')) {
            Flash::error(__('messages.flash.admin_not_found'));

            return redirect(route('admins.index'));
        }

        return view('admins.show')->with('admin', $admin);
    }

    /**
     * Show the form for editing the specified Admin.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $user = $this->adminRepository->find($id);

        if (empty($user) || ! $user->hasRole('Super Admin')) {
            Flash::error(__('messages.flash.admin_not_found'));

            return redirect(route('admins.index'));
        }

        return view('admins.edit')->with('user', $user);
    }

    /**
     * Update the specified Admin in storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(int $id, UpdateAdminRequest $request): RedirectResponse
    {
        $checkSuperAdmin = User::whereId($id)->where('is_super_admin_default', 1)->exists();
        if ($checkSuperAdmin) {
            Flash::error(__('messages.common.this_action_is_not_allowed_for_default_record'));

            return redirect(route('admins.index'));
        }

        $user = $this->adminRepository->find($id);

        $input = $request->all();
        $input['region_code'] = regionCode($input['prefix_code']);
        $this->adminRepository->update($user, $input);

        Flash::success(__('messages.admin_user.admin_updated_successfully'));

        return redirect(route('admins.index'));
    }

    /**
     * Remove the specified Admin from storage.
     *
     *
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $checkSuperAdmin = User::whereId($id)->where('is_super_admin_default', 1)->exists();
        if ($checkSuperAdmin) {
            return $this->sendError(__('messages.new_change.default_admin_not_delete'));
        }

        $user = User::find($id);

        if (empty($user) || ! $user->hasRole('Super Admin')) {
            return $this->sendError(__('messages.flash.admin_not_found'));
        }

        $user->delete();

        return $this->sendSuccess(__('messages.admin_user.admin_deleted_successfully'));
    }
}
