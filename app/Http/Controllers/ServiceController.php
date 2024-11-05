<?php

namespace App\Http\Controllers;

use App\Exports\ServiceExport;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\PackageService;
use App\Models\Service;
use App\Repositories\ServiceRepository;
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

class ServiceController extends AppBaseController
{
    /** @var ServiceRepository */
    private $serviceRepository;

    public function __construct(ServiceRepository $serviceRepo)
    {
        $this->serviceRepository = $serviceRepo;
    }

    /**
     * Display a listing of the Service.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = Service::STATUS_ARR;

        return view('services.index', $data);
    }

    /**
     * Show the form for creating a new Service.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        return view('services.create');
    }

    /**
     * Store a newly created Service in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateServiceRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['rate'] = removeCommaFromNumbers($input['rate']);
        $this->serviceRepository->create($input);
        $this->serviceRepository->createNotification();
        Flash::success(__('messages.flash.service_saved'));

        return redirect(route('services.index'));
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show(Service $service)
    {
        if (! canAccessRecord(Service::class, $service->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $service = $this->serviceRepository->find($service->id);
        if (empty($service)) {
            Flash::error(__('messages.flash.service_not_found'));

            return redirect(route('services.index'));
        }

        return view('services.show')->with('service', $service);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Service $service)
    {
        if (! canAccessRecord(Service::class, $service->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('services.edit', compact('service'));
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function update(Service $service, UpdateServiceRequest $request): RedirectResponse
    {
        if (empty($service)) {
            Flash::error(__('messages.flash.service_not_found'));

            return redirect(route('services.index'));
        }
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['rate'] = removeCommaFromNumbers($input['rate']);
        $this->serviceRepository->update($input, $service->id);
        Flash::success(__('messages.flash.service_updated'));

        return redirect(route('services.index'));
    }

    /**
     * Remove the specified Service from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Service $service): JsonResponse
    {
        if (! canAccessRecord(Service::class, $service->id)) {
            return $this->sendError(__('messages.flash.service_not_found'));
        }

        $serviceModel = [
            PackageService::class,
        ];
        $result = canDelete($serviceModel, 'service_id', $service->id);
        if ($result) {
            return $this->sendError(__('messages.flash.service_cant_deleted'));
        }
        $service->delete();

        return $this->sendSuccess(__('messages.flash.service_deleted'));
    }

    public function activeDeActiveService($id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->status = ! $service->status;
        $service->update(['status' => $service->status]);

        return $this->sendSuccess(__('messages.flash.service_updated'));
    }

    public function serviceExport(): BinaryFileResponse
    {
        $response = Excel::download(new ServiceExport, 'services-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
