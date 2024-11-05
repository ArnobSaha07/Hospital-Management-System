@php
    $settingValue = getSettingValue();
@endphp
<div class="row">
    <div class="form-group col-sm-12 my-0">
        <a href="{{ route('opd.prescriptions.pdf',$opdPrescription->id) }}" class="btn btn-sm btn-success float-end" target="_blank">
            <i class="fas fa fa-print"></i> {{ __('messages.ipd_patient_prescription.print_prescription') }}
        </a>
    </div>
</div>
<hr>
<div id='DivIdToPrint'>
    <div class="row">
        <div class="form-group col-sm-12 my-0">
            <div class="row">
                <div class="col-md-2">
                    <img src="{{ asset(getLogoUrl()) }}" class="w-75" alt="product-image"/>
                </div>
                <div class="col-md-10 text-end">
                    <p class="fw-bolder text-gray-800 fs-6 mb-2">{{ __('messages.common.address').':' }} <b
                                class="fw-bold fs-7 text-gray-600">{{ $settingValue['hospital_address']['value'] }}</b>
                    </p>
                    <p class="fw-bolder text-gray-800 fs-6 mb-2">{{ __('messages.user.phone').':' }} <b
                                class="fw-bold fs-7 text-gray-600">{{ $settingValue['hospital_phone']['value'] }}</b>
                    </p>
                    <p class="fw-bolder text-gray-800 fs-6 mb-2">{{ __('messages.user.email').':' }} <b
                                class="fw-bold fs-7 text-gray-600">{{ $settingValue['hospital_email']['value'] }}</b>
                    </p>
                    <p class="fw-bolder text-gray-800 fs-6 mb-2">{{ __('messages.common.created_on').':' }} <b
                                class="fw-bold fs-7 text-gray-600">{{ date('jS M, Y H:i', strtotime($opdPrescription->created_at))  }}</b>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-sm-12 my-0">
            <p class="my-0 mb-0 py-0 fw-bolder text-gray-800 fs-6" id="opdHeaderNoteData">
                {!! !empty($opdPrescription->header_note) ? nl2br(e($opdPrescription->header_note)) : __('messages.common.n/a') !!}
            </p>
        </div>
    </div>
    <hr>
    <div class="row g-5 mb-11">
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.opd_patient.opd_number').':' }}</div>
            <div class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->opd_number }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.bed_assign.patient_name').':' }}</div>
            <div class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->patient->user->full_name }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.user.email').':' }}</div>
            <div class="fw-bolder fs-6 text-gray-800 text-wrap text-break">{{ $opdPrescription->patient->patient->user->email }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.user.phone').':' }}</div>
            <div
                    class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->patient->user->phone != null ? $opdPrescription->patient->patient->user->region_code.$opdPrescription->patient->patient->user->phone : __('messages.common.n/a') }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.user.gender').':' }}</div>
            <div
                    class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->patient->user->gender == 0 ? __('messages.user.male') : __('messages.user.female') }}</div>
        </div>
        <div class="col-sm-2 ">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.blood_donor.age').':' }}</div>
            <div class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->patient->user->age }}</div>
        </div>
    </div>
    <div class="row g-5 mb-11">
        <div class="col-sm-2 ">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.opd_patient.appointment_date').':' }}</div>
            <div
                    class="fw-bolder fs-6 text-gray-800">{{ date('jS M, Y H:i', strtotime($opdPrescription->patient->appointment_date)) }}</div>
        </div>
        <div class="col-sm-2 ">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.case.case_id').':' }}</div>
            <div class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->patientCase->case_id }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.ipd_patient.doctor_id').':' }}</div>
            <div class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->doctor->user->full_name }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.ipd_patient.height').':' }}</div>
            <div
                    class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->height != null ? $opdPrescription->patient->height : __('messages.common.n/a') }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.ipd_patient.weight').':' }}</div>
            <div
                    class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->weight != null ? $opdPrescription->patient->weight : __('messages.common.n/a') }}</div>
        </div>
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.ipd_patient.bp').':' }}</div>
            <div
                    class="fw-bolder fs-6 text-gray-800">{{ $opdPrescription->patient->bp != null ? $opdPrescription->patient->bp : __('messages.common.n/a') }}</div>
        </div>
    </div>
    <div class="row g-5 mb-11">
        <div class="col-sm-2">
            <div class="fw-bold pe-10 text-gray-600 fs-7">{{ __('messages.ipd_patient.symptoms').':' }}</div>
            <div
                    class="fw-bolder fs-6 text-gray-800">{!!  $opdPrescription->patient->symptoms != null ? $opdPrescription->patient->symptoms : __('messages.common.n/a')  !!}</div>
        </div>
    </div>
    <div class="flex-grow-1 table-responsive">
        <table class="table  align-middle table-row-dashed fs-6 gy-5 dataTable no-footer w-100 mb-3">
            <thead>
            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                <th class="min-w-175px pb-2">{{ __('messages.ipd_patient_prescription.category_id') }}</th>
                <th class="min-w-70px pb-2">{{ __('messages.ipd_patient_prescription.medicine_id') }}</th>
                <th class="min-w-70px pb-2">{{ __('messages.ipd_patient_prescription.dosage') }}</th>
                <th class="min-w-70px pb-2">{{ __('messages.medicine_bills.dose_interval') }}</th>
                <th class="min-w-180px pb-2">{{ __('messages.ipd_patient_prescription.instruction') }}</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
            @foreach($opdPrescription->opdPrescriptionItems as $opdPrescriptionItem)
                <tr class="fw-bolder text-gray-700 fs-5 text-end">
                    <td class="text-start pt-6">{{ $opdPrescriptionItem->medicineCategory->name }}
                        - {{$loop->iteration}}</td>
                    <td class="text-start pt-6">{{ $opdPrescriptionItem->medicine->name }}</td>
                    <td class="text-start pt-6">
                        {{ $opdPrescriptionItem->dosage }}
                        @if($opdPrescriptionItem->time == 0)
                            {{__('messages.prescription.after_meal')}}
                        @else
                            {{__('messages.prescription.before_meal')}}
                        @endif
                    </td>
                    <td class="text-start pt-6">{{ App\Models\Prescription::DOSE_INTERVAL[$opdPrescriptionItem->dose_interval] ?? 'N\A' }}</td>
                    <td class="text-start pt-6">{!! !empty($opdPrescriptionItem->instruction) ? nl2br(e($opdPrescriptionItem->instruction)) : __('messages.common.n/a') !!}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <hr class="py-0 my-0 mb-3">
    <div class="row">
        <div class="form-group col-sm-12 my-0">
            <p class="my-0 mb-0 py-0 fw-bolder text-gray-800 fs-6" id="opdFooterNoteData">
                {!! !empty($opdPrescription->footer_note) ? nl2br(e($opdPrescription->footer_note)) : __('messages.common.n/a') !!}
            </p>
        </div>
    </div>
    <hr>
</div>
