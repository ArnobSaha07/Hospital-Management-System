@php
    $settingValue = getSettingValue();
@endphp

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "//www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="icon" href="{{ asset('web/img/logo.jpg') }}" type="image/png">
    <title>{{ __('messages.google_meet.opd_prescription') }}</title>
    <link href="{{ asset('assets/css/ipd-prescription-pdf.css') }}" rel="stylesheet" type="text/css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, "Helvetica", Arial, "Liberation Sans",
                sans-serif;
        }

        body {
            font-family: "Lato", sans-serif;
            padding: 0px 25px 0px 25px;
            font-size: 14px;
        }

        @page {
            margin: 20px 0 !important;
        }

        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50% !important;
        }

        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .ms-auto {
            margin-left: auto !important;
        }

        .px-30 {
            padding-left: 30px;
            padding-right: 30px;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .lh-1 {
            line-height: 1 !important;
        }

        .company-logo {
            margin: 0 auto;
        }

        .company-logo img {
            width: auto;
            height: 80px;
        }

        .vertical-align-top {
            vertical-align: top !important;
        }

        .desc {
            padding: 10px;
            border-radius: 10px;
            width: 50%;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        hr {
            margin: 15px 0px;
            color: #f8f9fa;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .fw-6 {
            font-weight: bold;
        }

        .mb-20 {
            margin-bottom: 15px;
        }

        .heading {
            padding: 10px;
            background-color: #f8f9fa;
            width: 250px;
        }

        .lh-2 {
            line-height: 1.5 !important;
        }

        .font-color-gray {
            color: #7a7a7a;
        }

        .main-heading {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-right {
            text-align: right;
            vertical-align: top;
        }

        .logo,
        .hospital-name {
            margin-bottom: 15px;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .address {
            margin-top: 60px;
            width: 100%;
        }

        .address tr:first-child td {
            padding-bottom: 10px;
        }

        .items-table {
            width: 100%;
            border: 0;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .items-table thead {
            background: #2f353a;
            color: #fff;
        }

        .items-table td,
        .items-table th {
            padding: 8px;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
        }

        .patient-details {
            vertical-align: top;
        }

        .patient-detail-one td,
        .patient-detail-two td {
            padding: 5px;
        }

        .patient-detail-two {
            float: left;
        }

        .patient-detail-heading {
            font-size: medium;
        }

        .footer-note {
            padding: 10px;
            border-radius: 10px;
            width: 97%;
            margin-top: 20px;
        }

        .ipdFooterNoteData {
            width: 95%;
            word-wrap: break-word;
            margin-top: 5px;
        }

        .header-note {
            padding: 10px;
            border-radius: 10px;
            width: 97%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .ipdHeaderNoteData {
            width: 95%;
            word-wrap: break-word;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td class="header-left">
                <div class="main-heading">{{ __('messages.ipd_patient_prescription.prescription_details') }}
                    {{ __('messages.reports') }}</div>
            </td>
            <td class="header-right">
                <div class="logo"><img width="100px" src="{{ getLogoUrl() }}" alt=""></div>
                <div>
                    <b>{{ __('messages.common.address') . ':' }}</b>{{ $settingValue['hospital_address']['value'] }}
                </div>
                <div>
                    <b>{{ __('messages.user.phone') . ':' }}</b>{{ $settingValue['hospital_phone']['value'] }}
                </div>
                <div class="hospital-name">
                    <b>{{ __('messages.user.email') . ':' }}</b>{{ $settingValue['hospital_email']['value'] }}
                </div>
            </td>
        </tr>
    </table>
    <hr>

    @if (!empty($opdPrescription->header_note))
        <div class="header-note bg-light">
            <table class="table w-100">
                <tbody>
                    <tr class="lh-2">
                        <label for="name"
                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.ipd_patient_prescription.header_note') }}:</label>
                    </tr>
                    <td>
                        <div class="ipdHeaderNoteData" id="opdHeaderNoteData">
                            {!! !empty($opdPrescription->header_note) ? nl2br(e($opdPrescription->header_note)) : __('messages.common.n/a') !!}
                        </div>
                    </td>
                </tbody>
            </table>
        </div>
    @endif


    <div class="">
        <table class="table w-100">
            <tbody>
                <tr>
                    <td class="desc vertical-align-top bg-light">
                        <table class="table w-100">
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name" class="pb-2 fs-5 text-gray-600 font-weight-bold">
                                        {{ __('messages.patient.patient_details') }}:
                                    </label>
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.investigation_report.patient') }}:</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->patient->user->full_name }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.user.email') }}:</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->patient->user->email }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.user.phone') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->patient->user->phone != null ? $opdPrescription->patient->patient->user->region_code.$opdPrescription->patient->patient->user->phone : __('messages.common.n/a') }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.user.gender') }}:</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->patient->user->gender == 0 ? __('messages.user.male') : __('messages.user.female') }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.blood_donor.age') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->patient->user->age }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.ipd_patient.height') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->height != null ? $opdPrescription->patient->height : __('messages.common.n/a') }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.ipd_patient.weight') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->weight != null ? $opdPrescription->patient->weight : __('messages.common.n/a') }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.ipd_patient.bp') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->bp != null ? $opdPrescription->patient->bp : __('messages.common.n/a') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:2%;">
                    </td>
                    <td class="text-end desc ms-auto vertical-align-top bg-light">
                        <table class="table w-100">
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.ipd_patient.doctor_id') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->doctor->user->full_name }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.opd_patient.opd_number') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->opd_number }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.opd_patient.appointment_date') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ date('jS M, Y H:i', strtotime($opdPrescription->patient->appointment_date)) }}
                                </td>
                            </tr>
                            <tr class="lh-2">
                                <td class="">
                                    <label for="name"
                                        class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.case.case_id') . ':' }}</label>
                                </td>
                                <td class="text-end fs-5 text-gray-800">
                                    {{ $opdPrescription->patient->patientCase ? $opdPrescription->patient->patientCase->case_id : __('messages.common.n/a') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if (!empty($opdPrescription->footer_note))
        <div class="footer-note bg-light">
            <table class="table w-100">
                <tbody>
                    <tr class="lh-2">
                        <label for="name"
                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.ipd_patient_prescription.footer_note') }}:</label>
                    </tr>
                    <td>
                        <div class="ipdFooterNoteData" id="ipdFooterNoteData">
                            {!! !empty($opdPrescription->footer_note) ? nl2br(e($opdPrescription->footer_note)) : __('messages.common.n/a') !!}
                        </div>
                    </td>
                </tbody>
            </table>
        </div>
    @endif


    <table width="100%">
        <tr>
            <td colspan="2">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.ipd_patient_prescription.category_id') }}</th>
                            <th>{{ __('messages.ipd_patient_prescription.medicine_id') }}</th>
                            <th>{{ __('messages.ipd_patient_prescription.dosage') }}</th>
                            <th>{{ __('messages.medicine_bills.dose_interval') }}</th>
                            <th>{{ __('messages.ipd_patient_prescription.instruction') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($opdPrescription->opdPrescriptionItems))
                            @foreach ($opdPrescription->opdPrescriptionItems as $opdPrescriptionItem)
                                <tr>
                                    <td>{{ $opdPrescriptionItem->medicineCategory->name }} -
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>{{ $opdPrescriptionItem->medicine->name }}</td>
                                    <td>{{ $opdPrescriptionItem->dosage }}@if ($opdPrescriptionItem->time == 0)
                                            ({{ __('messages.prescription.after_meal') }})
                                        @else
                                            ({{ __('messages.prescription.before_meal') }})
                                        @endif
                                    </td>
                                    <td>
                                        {{ App\Models\Prescription::DOSE_INTERVAL[$opdPrescriptionItem->dose_interval] ?? 'N\A' }}
                                    </td>
                                    <td>{!! !empty($opdPrescriptionItem->instruction)
                                        ? nl2br(e($opdPrescriptionItem->instruction))
                                        : __('messages.common.n/a') !!}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
