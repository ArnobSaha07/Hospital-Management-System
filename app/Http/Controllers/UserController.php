<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\DoctorDepartment;
use App\Models\User;
use App\Repositories\UserRepository;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;
use URL;

/**
 * Class UserController
 */
class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $input = $request->all();

        try {
            $user = $this->userRepository->changePassword($input);

            return $this->sendSuccess(__('messages.flash.password_update'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    public function profileUpdate(UpdateUserProfileRequest $request): JsonResponse
    {
        $input = $request->all();
        // $input['phone'] = preparePhoneNumber($input, 'phone');
        $input['region_code'] = regionCode($input['prefix_code']);

        try {
            $user = $this->userRepository->profileUpdate($input);

            return $this->sendResponse($user, __('messages.flash.profile_update'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    /**
     * Show the form for editing the specified User.
     */
    public function editProfile(): JsonResponse
    {
        $user = getLoggedInUser()->append('image_url');

        return $this->sendResponse($user, __('messages.flash.user_retrieved'));
    }

    public function updateLanguage(Request $request): JsonResponse
    {
        $language = $request->get('language');

        /** @var User $user */
        $user = $request->user();
        $user->update(['language' => $language]);

        return $this->sendSuccess(__('messages.flash.language_update'));
    }

    /**
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $userRole = ['Admin', 'Super Admin'];
        $roles = Department::whereNotIn('name', $userRole)->orderBy('name')->pluck('name', 'id')->toArray();
        $status = User::STATUS_ARR;

        if (getLoggedInUser()->hasRole('Super Admin')) {
            return view('super_admin.users.index', compact('roles', 'status'));
        }

        return view('users.index', compact('roles', 'status'));
    }

    public function create(): View
    {
        $isEdit = false;
        $userRole = ['Super Admin'];
        $role = Department::whereNotIn('name', $userRole)->orderBy('name')->groupBy('name')->pluck('name', 'id')->toArray();
        $doctorDepartments = DoctorDepartment::pluck('title', 'id')->toArray();

        return view('users.create', compact('isEdit', 'role', 'doctorDepartments'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['status'] = isset($input['status']) ? 1 : 0;
            $input['region_code'] = regionCode($input['prefix_code']);
            $this->userRepository->store($input);
            Flash::success(__('messages.flash.user_saved'));
            DB::commit();

            return redirect(route('users.index'));
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function show($user)
    {
        if (! canAccessRecord(User::class, $user->id)) {
            return Redirect::back();
        }

        $userData = $this->userRepository->getUserData($user);

        return view('users.show', compact('userData'));
    }

    /**
     * Show the form for editing the specified User.
     *
     * @return Application|Factory|View
     */
    public function edit(User $user)
    {
        if (! canAccessRecord(User::class, $user->id)) {
            return Redirect::back();
        }

        $role = Department::pluck('name', 'id')->all();
        $isEdit = true;

        return view('users.edit', compact('isEdit', 'user', 'role'));
    }

    /**
     * Update the specified User in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $input = $request->all();
        //        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['dob'] = (! empty($input['dob'])) ? $input['dob'] : null;
        // $input['phone'] = preparePhoneNumber($input, 'phone');
        $input['region_code'] = regionCode($input['prefix_code']);
        $this->userRepository->updateUser($input, $user->id);
        Flash::success(__('messages.flash.user_updated'));

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        if (! canAccessRecord(User::class, $user->id)) {
            return $this->sendError(__('messages.flash.user_not_found'));
        }

        $checkAdmin = User::whereId($user->id)->where('is_admin_default', 1)->exists();
        if ($checkAdmin) {
            return $this->sendError(__('messages.new_change.admin_not_delete'));
        }

        $this->userRepository->deleteUser($user->id);

        return $this->sendSuccess(__('messages.flash.user_deleted'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        $hospital = User::findOrFail($id);
        $status = ! $hospital->status;
        User::where('tenant_id', $hospital->tenant_id)->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function activeDeactiveUserStatus($id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->status == User::INACTIVE) {
            $user->update(['status' => User::ACTIVE]);
        } else {
            $user->update(['status' => User::INACTIVE]);
        }

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function showModal(User $user): JsonResponse
    {
        if (! canAccessRecord(User::class, $user->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        $users = $this->userRepository->getUserData($user->id);

        return $this->sendResponse($users, __('messages.flash.user_retrieved'));
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function hospitalIndex(Request $request)
    {
    }

    public function isVerified(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $emailVerified = $user->email_verified_at == null ? Carbon::now() : null;
        $user->update(['email_verified_at' => $emailVerified]);

        return $this->sendSuccess(__('messages.flash.email_verified'));
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function userImpersonateLogin(User $user): RedirectResponse
    {
        Auth::user()->impersonate($user);
        $url = redirectToDashboard();

        return redirect(url($url));
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function userImpersonateLogout(): RedirectResponse
    {
        Auth::user()->leaveImpersonation();

        return redirect(url('super-admin/dashboard'));
    }

    public function changeThemeMode(): RedirectResponse
    {
        $user = User::find(getLoggedInUser()->id);

        if ($user->theme_mode == User::LIGHT_MODE) {
            $user['theme_mode'] = User::DARK_MODE;
        } else {
            $user['theme_mode'] = User::LIGHT_MODE;
        }

        $user->update();

        return redirect(URL::previous());
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function impersonate(User $user): RedirectResponse
    {
        getLoggedInUser()->impersonate($user);

        return redirect(route('dashboard'));
    }
}
