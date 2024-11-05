<?php

namespace App\Http\Controllers;

use App\Exports\PostalExport;
use App\Http\Requests\PostalRequest;
use App\Http\Requests\UpdatePostalRequest;
use App\Models\Postal;
use App\Repositories\PostalRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
//use Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class PostalController
 */
class PostalController extends AppBaseController
{
    /**
     * @var postalRepository
     */
    private $postalRepository;

    /**
     * PostalController constructor.
     */
    public function __construct(PostalRepository $postalRepository)
    {
        $this->postalRepository = $postalRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        if (Route::current()->getName() == 'receives.index') {
            return view('postals.receives.index');
        }
        if (Route::current()->getName() == 'dispatches.index') {
            return view('postals.dispatches.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostalRequest $request): JsonResponse
    {
        $input = $request->all();

        $this->postalRepository->store($input);

        if (Route::current()->getName() == 'receives.store') {
            return $this->sendSuccess(__('messages.flash.postal_receive_saved'));
        }

        if (Route::current()->getName() == 'dispatches.store') {
            return $this->sendSuccess(__('messages.flash.postal_dispatch_saved'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Postal $postal): JsonResponse
    {
        if (! canAccessRecord(Postal::class, $postal->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        if (Route::current()->getName() == 'receives.edit') {
            return $this->sendResponse($postal, __('messages.flash.postal_receive_retrieved'));
        }

        if (Route::current()->getName() == 'dispatches.edit') {
            return $this->sendResponse($postal, __('messages.flash.postal_dispatch_retrieved'));
        }
    }

    public function update(UpdatePostalRequest $request, Postal $postal): JsonResponse
    {
        if (! canAccessRecord(Postal::class, $postal->id)) {
            return $this->sendError(__('messages.flash.postal_not_found'));
        }

        $this->postalRepository->updatePostal($request->all(), $postal->id);

        if (Route::current()->getName() == 'receives.update') {
            return $this->sendSuccess(__('messages.flash.postal_receive_update'));
        }

        if (Route::current()->getName() == 'dispatches.update') {
            return $this->sendSuccess(__('messages.flash.postal_dispatch_update'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Postal $postal): JsonResponse
    {
        if (! canAccessRecord(Postal::class, $postal->id)) {
            return $this->sendError(__('messages.flash.postal_not_found'));
        }

        $this->postalRepository->deleteDocument($postal->id);

        return $this->sendSuccess(__('messages.flash.postal_deleted'));
    }

    public function downloadMedia(Postal $postal): Response
    {
        [$file, $headers] = $this->postalRepository->downloadMedia($postal);

        return response($file, 200, $headers);
    }

    public function export(): BinaryFileResponse
    {
        if (Route::current()->getName() == 'receives.excel') {
            $response = Excel::download(new PostalExport, 'receive-'.time().'.xlsx');

            ob_end_clean();

            return $response;
        }

        if (Route::current()->getName() == 'dispatches.excel') {
            $response = Excel::download(new PostalExport, 'dispatch-'.time().'.xlsx');

            ob_end_clean();

            return $response;
        }
    }
}
