<?php

namespace App\Http\Controllers;

use \PDF;
use Flash;
use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SmartPatientCard;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use App\Repositories\PatientRepository;
use App\Http\Controllers\AppBaseController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Http\Requests\CreateSmartPatientCardRequest;
use App\Http\Requests\UpdateSmartPatientCardRequest;
use App\Models\User;
use Illuminate\Validation\Rules\Exists;

class SmartPatientCardController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('smart-patient-card-templates.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('smart-patient-card-templates.create');
    }

    public function store(CreateSmartPatientCardRequest $request)
    {
        $input = $request->all();
        $data = SmartPatientCard::create($input);

        Flash::success(__('messages.lunch_break.smart_card_template_saved'));

        return redirect(route('patient-smart-card-templates.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SmartPatientCard $patientSmartCardTemplate)
    {
        return view('smart-patient-card-templates.edit', compact('patientSmartCardTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSmartPatientCardRequest $request, SmartPatientCard $patientSmartCardTemplate)
    {
        $input = $request->all();
        $input['show_email'] = isset($input['show_email']) ? 1 : 0;
        $input['show_phone'] = isset($input['show_phone']) ? 1 : 0;
        $input['show_dob'] = isset($input['show_dob']) ? 1 : 0;
        $input['show_blood_group'] = isset($input['show_blood_group']) ? 1 : 0;
        $input['show_address'] = isset($input['show_address']) ? 1 : 0;
        $input['show_patient_unique_id'] = isset($input['show_patient_unique_id']) ? 1 : 0;

        $patientSmartCardTemplate->update($input);

        Flash::success(__('messages.lunch_break.smart_card_template_update'));

        return redirect(route('patient-smart-card-templates.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $templateExist = Patient::whereTenantId(getLoggedInUser()->tenant_id)->where('template_id', $id)->exists();

        if ($templateExist) {
            return $this->sendError(__('Smart Patient Card Template already in use.'));
        } else {
            SmartPatientCard::whereId($id)->delete();
        }
        return $this->sendSuccess(__('messages.flash.radiology_test_deleted'));
    }

    public function smartCardIndex()
    {
        $cardTemplates = SmartPatientCard::pluck('template_name', 'id')->toArray();
        $patients = Patient::with('patientUser')->whereNull('template_id')->whereHas('patientUser', function (Builder $query) {
            $query->where('status', 1);
        })->get()->pluck('patientUser.full_name', 'id')->sort();

        return view('smart-patient-cards.index', compact('cardTemplates', 'patients'));
    }

    public function smartCardStore(Request $request)
    {
        $input = $request->all();

        if ($input['patient'] == "all") {
            $patients = Patient::whereTenantId(getLoggedInUser()->tenant_id)->get();
        } elseif ($input['patient'] == "one") {
            $patients = Patient::whereTenantId(getLoggedInUser()->tenant_id)->whereId($input['patient_id'])->get();
        } else {
            $patients = Patient::whereTenantId(getLoggedInUser()->tenant_id)->whereNull('template_id')->get();
        }

        foreach ($patients as $patient) {
            $uniqueId = strtoupper(Patient::generateUniquePatientId());
            $patient->update(['template_id' => $input['template_id'], 'patient_unique_id' => $uniqueId]);
        }

        return $this->sendSuccess(__('messages.lunch_break.smart_card_saved'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function smartCardDestroy($id)
    {
        $patient = Patient::findOrFail($id);

        $patient->update(['template_id' => null]);

        return $this->sendSuccess(__('Smart Patient Card deleted successfully.'));
    }

    public function smartCardShow($id)
    {
        $patient = Patient::with(['patientUser', 'SmartCardTemplate', 'address'])->find($id);
        if (empty($patient->patient_unique_id)) {
            $patient->update(['patient_unique_id' => strtoupper(Patient::generateUniquePatientId())]);
        }
        $patient['profile'] = $patient->patientUser->image_url;

        return $this->sendResponse($patient, 'Smart Patient Card retrieved successfully.');
    }

    public function downloadSmartCard($id)
    {
        $data = [];
        $data['app_logo'] = getLogoUrl();
        $data['app_name'] = getAppName();
        $data['hospital_address'] = getSettingValueByKey('hospital_address');
        $user = User::whereTenantId(getLoggedInUser()->tenant_id)->first();
        if (!getLoggedinPatient()) {
            $data['user_name'] = getLoggedInUser()->username;
        } else {
            $data['user_name'] = $user->username;
        }
        $patient = Patient::with(['patientUser', 'SmartCardTemplate', 'address'])->find($id);

        if (empty($patient->patient_unique_id)) {
            $patient->update(['patient_unique_id' => strtoupper(Patient::generateUniquePatientId())]);
        }
        $url = $patient->patientUser->image_url;
        $arrUrl = explode('/', trim($url))[2];

        if ($arrUrl == "ui-avatars.com") {
            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($patient->patientUser->full_name) . "&size=100&rounded=true&color=fff&background=fc6369";
            $avatarData = file_get_contents($avatarUrl);
            $data['profile'] = base64_encode($avatarData);
        } else {
            $avatarUrl = $url; // Replace with the appropriate URL
            $avatarData = file_get_contents($avatarUrl);
            $data['profile'] = base64_encode($avatarData);
        }

        $pdf = PDF::loadView('smart-patient-cards.smart-patient-card-pdf', compact('patient', 'data'));

        return $pdf->download('patient-smart-card.pdf');
    }

    public function smartCardQrCode($id)
    {
        $patient = Patient::findOrFail($id);
        $user = User::whereTenantId(getLoggedInUser()->tenant_id)->first();

        if (!getLoggedinPatient()) {
            $url = route('patient.details', [getLoggedInUser()->first_name, $patient->patient_unique_id]);
        } else {
            $url = route('patient.details', [$user->first_name, $patient->patient_unique_id]);
        }

        $qrCode =  QrCode::size(90)->generate($url);

        return $qrCode;
    }

    public function changeTemplateStatus(Request $request, $id)
    {
        $patientIdCardTemplateData = SmartPatientCard::find($id);

        if (isset($request->color)) {
            $patientIdCardTemplateData->update(['header_color' => $request->color]);
        } else {
            $patientIdCardTemplateData->update([$request->name => $request->status]);
        }

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function getPatients()
    {
        $patients = Patient::with('patientUser')->whereNull('template_id')->whereTenantId(getLoggedInUser()->tenant_id)->whereHas('patientUser', function (Builder $query) {
            $query->where('status', 1);
        })->get()->pluck('patientUser.full_name', 'id')->sort();

        return $this->sendResponse($patients, 'Patients get successfully.');
    }
}
