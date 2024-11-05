<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "//www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="icon" href="{{ asset('web/img/hms-saas-favicon.ico') }}" type="image/png">
    <title>{{ __('messages.new_change.patient_diagnosis_test_report') }}</title>
    <link href="{{ asset('assets/css/diagnosis-test-pdf.css') }}" rel="stylesheet" type="text/css" />
    <style>
        * {
            font-family: DejaVu Sans, Arial, "Helvetica", Arial, "Liberation Sans", sans-serif;
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
            width: 48%;
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
    </style>
</head>

<body>
    <table width="100%" class="mb-20">
        <table width="100%">
            <tr>
                <td class="header-left">
                    <div class="main-heading">{{ __('messages.new_change.patient_diagnosis_test_report') }}</div>
                    <div class="invoice-number font-color-gray">{{ __('messages.new_change.diagnosis_report_id') }}
                        #{{ $patientDiagnosisTest->report_number }}</div>
                </td>
                <td class="header-right">
                    <div class="logo"><img width="100px" src="{{ $app_logo }}" alt=""></div>
                    <div class="hospital-name">{{ $app_name }}</div>
                    <div class="hospital-name font-color-gray">{{ $hospital_address }}</div>
                </td>
            </tr>
        </table>
        {{-- <div class="">
        <table>
            <tbody>
                <tr>
                    <td class="company-logo">
                        <div class="main-heading">{{ __('messages.new_change.patient_diagnosis_test_report') }}</div>
                        <div class="invoice-number font-color-gray">{{ __('messages.new_change.diagnosis_report_id') }}
                            #{{ $patientDiagnosisTest->report_number }}</div>
                    </td>
                    <td class="text-end">
                        <div class="logo"><img width="100px" src="{{ $app_logo }}" alt=""></div>
                        <div class="hospital-name">{{ $app_name }}</div>
                        <div class="hospital-name font-color-gray">{{ $hospital_address }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div> --}}
        <hr>
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
                                        {{ $patientDiagnosisTest->patient->user->full_name }}
                                    </td>
                                </tr>
                                <tr class="lh-2">
                                    <td class="">
                                        <label for="name"
                                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.user.email') }}:</label>
                                    </td>
                                    <td class="text-end fs-5 text-gray-800">
                                        {{ $patientDiagnosisTest->patient->user->email }}
                                    </td>
                                </tr>
                                <tr class="lh-2">
                                    <td class="">
                                        <label for="name"
                                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.bill.cell_no') }}:</label>
                                    </td>
                                    <td class="text-end fs-5 text-gray-800">
                                        {{ !empty($patientDiagnosisTest->patient->user->phone) ? $patientDiagnosisTest->patient->user->region_code.$patientDiagnosisTest->patient->user->phone : __('messages.common.n/a') }}
                                    </td>
                                </tr>
                                <tr class="lh-2">
                                    <td class="">
                                        <label for="name"
                                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.user.gender') }}:</label>
                                    </td>
                                    <td class="text-end fs-5 text-gray-800">
                                        {{ $patientDiagnosisTest->patient->user->gender == 0 ? 'Male' : 'Female' }}
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
                                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.ipd_patient_diagnosis.report_date') }}:</label>
                                    </td>
                                    <td class="text-end fs-5 text-gray-800">
                                        {{ \Carbon\Carbon::parse($patientDiagnosisTest->created_at)->translatedFormat('jS M,Y g:i A') }}
                                    </td>
                                </tr>
                                <tr class="lh-2">
                                    <td class="">
                                        <label for="name"
                                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.investigation_report.doctor') }}:</label>
                                    </td>
                                    <td class="text-end fs-5 text-gray-800">
                                        {{ $patientDiagnosisTest->doctor->user->full_name }}
                                    </td>
                                </tr>
                                <tr class="lh-2">
                                    <td class="">
                                        <label for="name"
                                            class="pb-2 fs-5 font-weight-bold me-1">{{ __('messages.diagnosis_category.diagnosis_category') }}:</label>
                                    </td>
                                    <td class="text-end fs-5 text-gray-800">
                                        {{ $patientDiagnosisTest->category->name }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- <tr>
            <td colspan="2">
                <table class="address">
                    <tr>
                        <td>
                            <span
                                class="font-weight-bold patient-detail-heading">{{ __('messages.ipd_patient_diagnosis.report_date') }}:</span>
                            {{ \Carbon\Carbon::parse($patientDiagnosisTest->created_at)->translatedFormat('jS M,Y g:i A') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="font-weight-bold patient-detail-heading">
                            {{ __('messages.patient.patient_details') }}:
                        </td>
                    </tr>
                    <tr>
                        <td class="patient-details">
                            <table class="patient-detail-one">
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.investigation_report.patient') }}:
                                    </td>
                                    <td>{{ $patientDiagnosisTest->patient->user->full_name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.user.email') }}:</td>
                                    <td>{{ $patientDiagnosisTest->patient->user->email }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.bill.cell_no') }}:</td>
                                    <td>{{ !empty($patientDiagnosisTest->patient->user->phone) ? $patientDiagnosisTest->patient->user->phone : 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.user.gender') }}:</td>
                                    <td>{{ $patientDiagnosisTest->patient->user->gender == 0 ? 'Male' : 'Female' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="patient-detail-two">
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.investigation_report.doctor') }}:</td>
                                    <td>{{ $patientDiagnosisTest->doctor->user->full_name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">
                                        {{ __('messages.diagnosis_category.diagnosis_category') }}
                                        :
                                    </td>
                                    <td> {{ $patientDiagnosisTest->category->name }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr> --}}
        <table width="100%">
            <tr>
                <td colspan="2">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.patient_diagnosis_test.diagnosis_property_name') }}</th>
                                <th>{{ __('messages.patient_diagnosis_test.diagnosis_property_value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($patientDiagnosisTests))
                                @foreach ($patientDiagnosisTests as $key => $patientDiagnosisTest)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ str_replace('_', ' ', Str::title($patientDiagnosisTest->property_name)) }}
                                        </td>
                                        <td>{{ !empty($patientDiagnosisTest->property_value) ? $patientDiagnosisTest->property_value : 'N/A' }}
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
