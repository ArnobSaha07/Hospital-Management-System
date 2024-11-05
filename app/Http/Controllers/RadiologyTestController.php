<?php

namespace App\Http\Controllers;

use App\Exports\RadiologyTestExport;
use App\Http\Requests\CreateRadiologyTestRequest;
use App\Http\Requests\UpdateRadiologyTestRequest;
use App\Models\Charge;
use App\Models\RadiologyTest;
use App\Repositories\RadiologyTestRepository;
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

class RadiologyTestController extends AppBaseController
{
    /** @var RadiologyTestRepository */
    private $radiologyTestRepository;

    public function __construct(RadiologyTestRepository $radiologyTestRepo)
    {
        $this->middleware('check_menu_access');
        $this->radiologyTestRepository = $radiologyTestRepo;
    }

    /**
     * Display a listing of the RadiologyTest.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('radiology_tests.index');
    }

    /**
     * Show the form for creating a new RadiologyTest.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $data = $this->radiologyTestRepository->getRadiologyAssociatedData();

        return view('radiology_tests.create', compact('data'));
    }

    /**
     * Store a newly created RadiologyTest in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateRadiologyTestRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $input['report_days'] = ! empty($input['report_days']) ? $input['report_days'] : null;
        $input['subcategory'] = ! empty($input['subcategory']) ? $input['subcategory'] : null;
        $this->radiologyTestRepository->create($input);
        Flash::success(__('messages.flash.radiology_test_saved'));

        return redirect(route('radiology.test.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(RadiologyTest $radiologyTest)
    {
        if (! canAccessRecord(RadiologyTest::class, $radiologyTest->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('radiology_tests.show', compact('radiologyTest'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(RadiologyTest $radiologyTest)
    {
        if (! canAccessRecord(RadiologyTest::class, $radiologyTest->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $data = $this->radiologyTestRepository->getRadiologyAssociatedData();
        $data['chargeCodes'] = Charge::where('charge_category_id',$radiologyTest->charge_category_id)->orderBy('code')->pluck('code', 'id');

        return view('radiology_tests.edit', compact('radiologyTest', 'data'));
    }

    /**
     * Update the specified RadiologyTest in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(RadiologyTest $radiologyTest, UpdateRadiologyTestRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $input['report_days'] = ! empty($input['report_days']) ? $input['report_days'] : null;
        $input['subcategory'] = ! empty($input['subcategory']) ? $input['subcategory'] : null;
        $this->radiologyTestRepository->update($input, $radiologyTest->id);
        Flash::success(__('messages.flash.radiology_test_updated'));

        return redirect(route('radiology.test.index'));
    }

    /**
     * Remove the specified RadiologyTest from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(RadiologyTest $radiologyTest): JsonResponse
    {
        if (! canAccessRecord(RadiologyTest::class, $radiologyTest->id)) {
            return $this->sendError(__('messages.flash.radiology_test_not_found'));
        }

        $radiologyTest->delete();

        return $this->sendSuccess(__('messages.flash.radiology_test_deleted'));
    }

    /**
     * @return mixed
     */
    public function getStandardCharge($id)
    {
        $standardCharges = Charge::where('id', $id)->value('standard_charge');

        return $this->sendResponse($standardCharges, __('messages.flash.Standard_charge_retrieved'));
    }

    public function getChargeCode($id)
    {
        $chargeCodes = Charge::where('charge_category_id', $id)->pluck('code','id')->toArray();

        return $this->sendResponse($chargeCodes, __('messages.flash.Standard_charge_retrieved'));
    }

    public function radiologyTestExport(): BinaryFileResponse
    {
        $response = Excel::download(new RadiologyTestExport, 'radiology-tests-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    /**
     * @return JsonResponse|RedirectResponse
     */
    public function showModal(RadiologyTest $radiologyTest)
    {
        if (! canAccessRecord(RadiologyTest::class, $radiologyTest->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $radiologyTest->load(['radiologycategory', 'chargecategory']);

        return $this->sendResponse($radiologyTest, __('messages.flash.radiology_test_retrieved'));
    }
}
