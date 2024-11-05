<?php

namespace App\Http\Controllers;

use App\Exports\PackageExport;
use App\Http\Requests\CreatePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Package;
use App\Models\PatientAdmission;
use App\Repositories\PackageRepository;
use DB;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PackageController extends AppBaseController
{
    /** @var PackageRepository */
    private $packageRepository;

    public function __construct(PackageRepository $packageRepo)
    {
        $this->packageRepository = $packageRepo;
    }

    /**
     * Display a listing of the Package.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('packages.index');
    }

    /**
     * Show the form for creating a new Package.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $servicesList = $this->packageRepository->getServicesList();
        $services = $this->packageRepository->getServices();

        return view('packages.create', compact('services', 'servicesList'));
    }

    /**
     * Store a newly created Package in storage.
     *
     *
     * @throws Exception
     */
    public function store(CreatePackageRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $package = $this->packageRepository->store($request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($package, __('messages.flash.package_saved'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Package $package)
    {
        if (! canAccessRecord(Package::class, $package->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            $packageHasPatient = PatientAdmission::wherePatientId(getLoggedInUser()->owner_id)->wherePackageId($package->id)->exists();
            if (! $packageHasPatient) {
                return Redirect::back();
            }
        }

        $package = Package::with(['packageServicesItems.service'])->findOrFail($package->id);

        return view('packages.show')->with('package', $package);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Package $package)
    {
        if (! canAccessRecord(Package::class, $package->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $package->packageServicesItems;
        $servicesList = $this->packageRepository->getServicesList();
        $services = $this->packageRepository->getServices();

        return view('packages.edit', compact('services', 'package', 'servicesList'));
    }

    /**
     * @throws Exception
     */
    public function update(Package $package, UpdatePackageRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $package = $this->packageRepository->updatePackage($package->id, $request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($package, __('messages.flash.package_updated'));
    }

    /**
     * Remove the specified Package from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Package $package): JsonResponse
    {
        if (! canAccessRecord(Package::class, $package->id)) {
            return $this->sendError(__('messages.flash.package_not_found'));
        }

        $packageModel = [
            PatientAdmission::class,
        ];
        $result = canDelete($packageModel, 'package_id', $package->id);
        if ($result) {
            return $this->sendError(__('messages.flash.package_cant_deleted'));
        }
        $this->packageRepository->delete($package->id);

        return $this->sendSuccess(__('messages.flash.package_deleted'));
    }

    public function packageExport(): BinaryFileResponse
    {
        $response = Excel::download(new PackageExport, 'packages-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
