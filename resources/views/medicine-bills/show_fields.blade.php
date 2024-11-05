


<div class="d-flex align-items-center pb-10">
    <img alt="Logo" src="{{ asset(getLogoUrl()) }}" height="100px" width="100px">
    <a target="_blank"
     href="{{ route('medicine.bill.pdf',[$medicineBill->id]) }}"
       class="btn btn-success ms-auto text-white">{{ __('messages.bill.print_bill') }}</a>
</div>
<div class="m-0">
    <div class="fs-3 text-gray-800 mb-8"> #{{ $medicineBill->bill_number }}</div>
    <div class="row g-5 mb-11">
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.case.patient').':' }}</div>
            <div class="fs-5 text-gray-800">{{ $medicineBill->patient->patientUser->full_name }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.bill_date').':' }}</div>
            <div class="fs-5 text-gray-800">{{ Carbon\Carbon::parse($medicineBill->bill_date)->format('jS M, Y g:i A') }}</div>
        </div>
        {{--  <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.admission_id').':' }}</div>
            <div class="fs-5 text-gray-800">{{ $medicineBill->patientAdmission->patient_admission_id }}</div>
        </div>  --}}
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.patient_email').':' }}</div>
            <div class="fs-5 text-gray-800">{{ $medicineBill->patient->patientUser->email }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.ipd_patient.bill_status').':' }}</div>
            <div class="fs-5 text-gray-800">{{ App\Models\MedicineBill::PAYMENT_STATUS_ARRAY[$medicineBill->payment_status] }}</div>
        </div>
    </div>
    <div class="row g-5 mb-11">
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.patient_cell_no').':' }}</div>
            <div class="fs-5 text-gray-800">{{ !empty($medicineBill->patient->patientUser->phone) ? $medicineBill->patient->patientUser->region_code.$medicineBill->patient->patientUser->phone : __('messages.common.n/a') }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.patient_gender').':' }}</div>
            <div class="fs-5 text-gray-800">{{ (!$medicineBill->patient->patientUser->gender) ? __('messages.user.male') : __('messages.user.female') }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.patient_dob').':' }}</div>
            <div class="fs-5 text-gray-800">{{ (!empty($medicineBill->patient->patientUser->dob)) ? \Carbon\Carbon::parse($medicineBill->patient->patientUser->dob)->translatedFormat('jS M, Y') : __('messages.common.n/a') }}</div>
        </div>
        {{--  <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.case.doctor').':' }}</div>
            <div class="fs-5 text-gray-800">{{ $medicineBill->patientAdmission->doctor->doctorUser->full_name }}</div>
        </div>  --}}
    </div>
    <div class="row g-5 mb-11">
        {{--  <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.admission_date').':' }}</div>
            <div class="fs-5 text-gray-800">{{ \Carbon\Carbon::parse($medicineBill->patientAdmission->admission_date)->translatedFormat('jS M, Y g:i A') }}</div>
        </div>  --}}
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.discharge_date').':' }}</div>
            <div class="fs-5 text-gray-800">{{ !empty($medicineBill->patientAdmission->discharge_date)?date('jS M, Y g:i A', strtotime($medicineBill->patientAdmission->discharge_date)):'N/A' }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.package_name').':' }}</div>
            <div class="fs-5 text-gray-800">{{ !empty($medicineBill->patientAdmission->package->name)?$medicineBill->patientAdmission->package->name:'N/A' }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.insurance_name').':' }}</div>
            <div class="fs-5 text-gray-800">{{ !empty($medicineBill->patientAdmission->insurance->name)?$medicineBill->patientAdmission->insurance->name:'N/A' }}</div>
        </div>
    </div>
    <div class="row g-5 mb-11">
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.total_days').':' }}</div>
            <div class="fs-5 text-gray-800">{{ $medicineBill->totalDays ?? __('messages.common.n/a') }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.bill.policy_no').':' }}</div>
            <div class="fs-5 text-gray-800">{{ !empty($medicineBill->patientAdmission->policy_no) ? $medicineBill->patientAdmission->policy_no : __('messages.common.n/a') }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.common.created_on').':' }}</div>
            <div class="fs-5 text-gray-800">{{ $medicineBill->created_at->diffForHumans() }}</div>
        </div>
        <div class="col-sm-3">
            <div class="pb-2 fs-5 text-gray-600">{{ __('messages.common.last_updated').':' }}</div>
            <div class="fs-5 text-gray-800">{{ $medicineBill->created_at->diffForHumans() }}</div>
        </div>
    </div>
    <div class="flex-grow-1">
        <table class="table border-bottom-2">
            <thead>
            <tr class="border-bottom fs-6 fw-bolder text-muted">
                <th class="min-w-175px pb-2">{{ __('messages.bill.item_name') }}</th>
                <th class="min-w-70px text-end pb-2">{{ __('messages.bill.qty') }}</th>
                <th class="min-w-70px text-end pb-2">{{ __('messages.bill.price') }}</th>
                <th class="min-w-80px text-end pb-2">{{ __('messages.purchase_medicine.tax') }}</th>
                <th class="min-w-80px text-end pb-2">{{ __('messages.bill.amount') }}</th>


            </tr>
            </thead>
            <tbody>
            @foreach($medicineBill->saleMedicine as $index => $saleMedicine)
                <tr class="text-gray-700 fs-5 text-end">
                    <td class="d-flex align-items-center pt-6 text-gray-700">{{ isset($saleMedicine->medicine->name) ? $saleMedicine->medicine->name : __('messages.common.n/a')  }}</td>
                    <td class="pt-6 text-gray-700">{{ $saleMedicine->sale_quantity }}</td>
                    <td class="pt-6 text-gray-700">
                        {{ getCurrencyFormat($saleMedicine->sale_price ) }}</td>
                        <td class="pt-6 text-dark fw-boldest">
                            {{ $saleMedicine->tax.'%' }}</td>
                    <td class="pt-6 text-dark fw-boldest">
                        {{ getCurrencyFormat($saleMedicine->sale_price * $saleMedicine->sale_quantity) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-6 ms-lg-auto mt-4">
        <div class="border-top">
            <table class="table table-borderless  box-shadow-none mb-0 mt-5 text-end">
                <tbody>
                <tr>
                    <td class="ps-0">{{ __('messages.purchase_medicine.total').(':') }}</td>
                    <td class="text-gray-900 text-end pe-0">
                       {{ number_format($medicineBill->total,2) }}                                                                                         </td>
                    </tr>
                <tr>
                    <td class="ps-0">{{ __('messages.purchase_medicine.tax').(':') }}</td>
                    <td class="text-gray-900 text-end pe-0">
                   {{ number_format($medicineBill->tax_amount,2)}}
                    </td>
                </tr>
                <tr>
                    <td class="ps-0">{{ __('messages.purchase_medicine.discount').(':') }}</td>
                    <td class="text-gray-900 text-end pe-0">
                   {{ number_format($medicineBill->discount,2)}}
                    </td>
                </tr>
                <tr>
                    <td class="ps-0">{{ __('messages.purchase_medicine.net_amount').(':') }}</td>
                    <td class="text-gray-900 text-end pe-0">
                   {{ number_format($medicineBill->net_amount,2)}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
