<?php

namespace App\Http\Controllers;

use App\Exports\CallLogExport;
use App\Http\Requests\CreateCallLogRequest;
use App\Http\Requests\UpdateCallLogRequest;
use App\Models\CallLog;
use App\Repositories\CallLogRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class CallLogController
 */
class CallLogController extends AppBaseController
{
    /**
     * @var  CallLogRepository
     */
    private $CallLogRepository;

    /**
     * CallLogController constructor.
     */
    public function __construct(CallLogRepository $callLogRepo)
    {
        $this->CallLogRepository = $callLogRepo;
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
        $callTypeArr = CallLog::CALLTYPE_ARR;

        return view('call_logs.index', compact('callTypeArr'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create(): View
    {
        return view('call_logs.create');
    }

    /**
     * Store a newly created CallLog in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreateCallLogRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['phone'] = preparePhoneNumber($input, 'phone');
        $this->CallLogRepository->create($input);
        Flash::success(__('messages.flash.call_log_saved'));

        return redirect(route('call_logs.index'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(CallLog $callLog)
    {
        if (! canAccessRecord(CallLog::class, $callLog->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('call_logs.edit', compact('callLog'));
    }

    /**
     * Update the specified CallLog in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(UpdateCallLogRequest $request, CallLog $callLog): RedirectResponse
    {
        $input = $request->all();
        $input['phone'] = preparePhoneNumber($input, 'phone');
        $this->CallLogRepository->update($input, $callLog->id);
        Flash::success(__('messages.flash.call_log_updated'));

        return redirect(route('call_logs.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     *
     **/
    public function destroy(CallLog $callLog): JsonResponse
    {
        if (! canAccessRecord(CallLog::class, $callLog->id)) {
            return $this->sendError(__('messages.flash.call_log_not_found'));
        }

        $callLog->delete();

        return $this->sendSuccess(__('messages.flash.call_log_deleted'));
    }

    public function export(): BinaryFileResponse
    {
        $response = Excel::download(new CallLogExport, 'call-logs-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
