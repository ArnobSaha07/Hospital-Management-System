<?php

namespace App\Http\Controllers;

use App\Exports\VisitorExport;
use App\Http\Requests\CreateVisitorRequest;
use App\Http\Requests\UpdateVisitorRequest;
use App\Models\Visitor;
use App\Repositories\VisitorRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class VisitorController
 */
class VisitorController extends AppBaseController
{
    /**
     * @var  visitorRepository
     */
    private $visitorRepository;

    /**
     * VisitorController constructor.
     */
    public function __construct(VisitorRepository $visitorRepo)
    {
        $this->visitorRepository = $visitorRepo;
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
        $purpose = Visitor::FILTER_PURPOSE;

        return view('visitors.index', compact('purpose'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create(): View
    {
        $purpose = Visitor::PURPOSE;
        $isEdit = false;

        return view('visitors.create', compact('purpose', 'isEdit'));
    }

    /**
     * Store a newly created Visitor in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreateVisitorRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['phone'] = preparePhoneNumber($input, 'phone');
        $this->visitorRepository->store($input);
        Flash::success(__('messages.flash.visitor_saved'));

        return redirect(route('visitors.index'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Visitor $visitor)
    {
        if (! canAccessRecord(Visitor::class, $visitor->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $purpose = Visitor::PURPOSE;
        $fileExt = pathinfo($visitor->document_url, PATHINFO_EXTENSION);
        $isEdit = true;

        return view('visitors.edit', compact('visitor', 'purpose', 'fileExt', 'isEdit'));
    }

    /**
     * Update the specified Visitor in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(UpdateVisitorRequest $request, Visitor $visitor): RedirectResponse
    {
        $input = $request->all();
        $input['phone'] = preparePhoneNumber($input, 'phone');
        $this->visitorRepository->updateVisitor($input, $visitor->id);
        Flash::success(__('messages.flash.visitor_updated'));

        return redirect(route('visitors.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     *
     **/
    public function destroy(Visitor $visitor): JsonResponse
    {
        if (! canAccessRecord(Visitor::class, $visitor->id)) {
            return $this->sendError(__('messages.flash.visitor_not_found'));
        }

        $this->visitorRepository->deleteDocument($visitor->id);

        return $this->sendSuccess(__('messages.flash.visitor_deleted'));
    }

    /**
     * @return Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function downloadMedia(Visitor $visitor): Response
    {
        [$file, $headers] = $this->visitorRepository->downloadMedia($visitor);

        return response($file, 200, $headers);
    }

    public function export(): BinaryFileResponse
    {
        $response = Excel::download(new VisitorExport, 'visitor-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
