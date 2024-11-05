<?php

namespace App\Http\Controllers;

use App\Exports\PathologyTestExport;
use App\Http\Requests\CreatePathologyTestRequest;
use App\Http\Requests\UpdatePathologyTestRequest;
use App\Models\Charge;
use App\Models\PathologyParameter;
use App\Models\PathologyParameterItem;
use App\Models\PathologyTest;
use App\Repositories\AppointmentRepository;
use App\Repositories\PathologyTestRepository;
use App\Repositories\PatientRepository;
use Exception;
use Flash;
use \PDF;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PathologyTestController extends AppBaseController
{
    /** @var PathologyTestRepository */
    private $pathologyTestRepository;

    /** @var PatientRepository*/
    private $patientRepository;


    public function __construct(PathologyTestRepository $pathologyTestRepo, PatientRepository $patientRepository,)
    {
        $this->middleware('check_menu_access');
        $this->pathologyTestRepository = $pathologyTestRepo;
        $this->patientRepository = $patientRepository;
    }

    /**
     * Display a listing of the PathologyTest.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('pathology_tests.index');
    }

    /**
     * Show the form for creating a new PathologyTest.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $data = $this->pathologyTestRepository->getPathologyAssociatedData();
        $parameterList = $this->pathologyTestRepository->getParameterDataList();
        $patients = $this->patientRepository->getPatients();

        return view('pathology_tests.create', compact('data','parameterList','patients'));
    }

    /**
     * Store a newly created PathologyTest in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePathologyTestRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $input['unit'] = ! empty($input['unit']) ? $input['unit'] : null;
        $input['subcategory'] = ! empty($input['subcategory']) ? $input['subcategory'] : null;
        $input['method'] = ! empty($input['method']) ? $input['method'] : null;
        $input['report_days'] = ! empty($input['report_days']) ? $input['report_days'] : null;

        if ($input['parameter_id']) {
            foreach ($input['parameter_id'] as $key => $value) {
                if($input['parameter_id'][$key] == null){
                    Flash::error(__('messages.new_change.parameter_name_required'));
                    return redirect()->back();
                }
                if($input['patient_result'][$key] == null){
                    Flash::error(__('messages.new_change.patient_result_required'));
                    return redirect()->back();
                }
            }
        }
        $this->pathologyTestRepository->store($input);
        Flash::success(__('messages.flash.pathology_test_saved'));

        return redirect(route('pathology.test.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(PathologyTest $pathologyTest)
    {
        if (! canAccessRecord(PathologyTest::class, $pathologyTest->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }
        $pathologyParameterItems = PathologyParameterItem::with('pathologyTest','pathologyParameter.pathologyUnit')->wherePathologyId($pathologyTest->id)->get();

        return view('pathology_tests.show', compact('pathologyTest', 'pathologyParameterItems'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(PathologyTest $pathologyTest)
    {
        if (! canAccessRecord(PathologyTest::class, $pathologyTest->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $data = $this->pathologyTestRepository->getPathologyAssociatedData();
        $parameterList = $this->pathologyTestRepository->getParameterDataList();
        $pathologyParameterItems = $this->pathologyTestRepository->getParameterItemData($pathologyTest->id);
        $patients = $this->patientRepository->getPatients();


        return view('pathology_tests.edit', compact('pathologyTest', 'data', 'parameterList', 'pathologyParameterItems','patients'));
    }

    /**
     * Update the specified PathologyTest in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(PathologyTest $pathologyTest, UpdatePathologyTestRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $input['unit'] = ! empty($input['unit']) ? $input['unit'] : null;
        $input['subcategory'] = ! empty($input['subcategory']) ? $input['subcategory'] : null;
        $input['method'] = ! empty($input['method']) ? $input['method'] : null;
        $input['report_days'] = ! empty($input['report_days']) ? $input['report_days'] : null;

        if ($input['parameter_id']) {
            foreach ($input['parameter_id'] as $key => $value) {
                if($input['parameter_id'][$key] == null){
                    Flash::error(__('messages.new_change.parameter_name_required'));
                    return redirect()->back();
                }
                if($input['patient_result'][$key] == null){
                    Flash::error(__('messages.new_change.patient_result_required'));
                    return redirect()->back();
                }
            }
        }

        $this->pathologyTestRepository->update($input, $pathologyTest);
        Flash::success(__('messages.flash.pathology_test_updated'));

        return redirect(route('pathology.test.index'));
    }

    /**
     * Remove the specified PathologyTest from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(PathologyTest $pathologyTest): JsonResponse
    {
        if (! canAccessRecord(PathologyTest::class, $pathologyTest->id)) {
            return $this->sendError(__('messages.flash.pathology_test_not_found'));
        }
        $pathologyTest->parameterItems()->delete();
        $pathologyTest->delete();

        return $this->sendSuccess(__('messages.flash.pathology_test_deleted'));
    }

    /**
     * @return mixed
     */
    public function getStandardCharge($id)
    {
        $standardCharges = Charge::where('charge_category_id', $id)->value('standard_charge');

        return $this->sendResponse($standardCharges, __('messages.flash.Standard_charge_retrieved'));
    }

    public function pathologyTestExport(): BinaryFileResponse
    {
        $response = Excel::download(new PathologyTestExport, 'pathology-tests-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function showModal(PathologyTest $pathologyTest)
    {
        if (! canAccessRecord(PathologyTest::class, $pathologyTest->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        $pathologyTest->load(['pathologycategory', 'chargecategory']);

        return $this->sendResponse($pathologyTest, __('messages.flash.pathology_test_retrieved'));
    }

    public function getPathologyParameter($id): JsonResponse
    {
        $data = [];
        $data['parameter'] = PathologyParameter::with('pathologyUnit')->whereId($id)->first();

        return $this->sendResponse($data, 'retrieved');
    }

    public function convertToPDF($id): \Illuminate\Http\Response
    {
        $data = [];
        $data['logo'] = $this->pathologyTestRepository->getSettingList();
        $data['pathologyTest'] = PathologyTest::with(['pathologycategory', 'chargecategory'])->where('id',$id)->first();
        $data['pathologyParameterItems'] = PathologyParameterItem::with('pathologyTest','pathologyParameter.pathologyUnit')->wherePathologyId($id)->get();

        $pdf = PDF::loadView('pathology_tests.pathology_test_pdf', compact('data'));

        return $pdf->stream('Pathology Test');
    }
}
