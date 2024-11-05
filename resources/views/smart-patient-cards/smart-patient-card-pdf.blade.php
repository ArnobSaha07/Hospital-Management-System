<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "//www.w3.org/TR/html4/strict.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="icon" href="{{ asset('web/img/hms-saas-favicon.ico') }}" type="image/png">
    <title>{{ __('messages.lunch_break.smart_patient_card') }}</title>
    <link href="{{ asset('assets/css/prescription-pdf.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
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

        .mx-3 {
            margin-left: 10px;
            margin-right: 10px;
        }

        .my-3 {
            margin-top: 10px;
            margin-bottom: 10px !important;
        }

        .my-4 {
            margin-top: 15px;
            margin-bottom: 10px !important;
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
            line-height: 1.2 !important;
        }

        .company-logo {
            margin: 0 auto;
        }

        .logo img {
            width: auto;
            height: 60px;
        }

        .fw-6 {
            font-weight: bold;
        }

        .fw-l {
            font-weight: light;
        }

        .mb-20 {
            margin-bottom: 15px;
        }

        .pe-0 {
            padding-right: 0;
            padding-left: 30px;
        }

        .fs-12 {
            font-size: 12px;
        }

        .fs-15 {
            font-size: 15px;
        }

        .flex-1 {
            flex: 1;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .patient-card {
            min-width: 650px !important;
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }

        .patient-card-body {
            padding: 20px 25px;
        }

        .patient-card-body .user-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            overflow: hidden;
            margin: auto;
        }

        .patient-card-body .patient-desc tr td {
            height: 10px;
            padding: 1px 0;
            font-size: 15px;
            color: #5a5a5a;
        }

        .word-break {
            word-break: break-word !important;
            width: 220px !important;
        }

        .patient-card-body .qr-code {
            width: 80px;
            height: 80px;
        }

        .patient-card-body .address-text {
            font-size: 15px;
            color: #5a5a5a;
        }

        .patient-card-body .signature-text {
            color: #909090;
        }

        .colorBox {
            height: 30px;
            width: 60px;
            border-radius: 5px;
        }

        .me-4 {
            margin-right: 15px;
            margin-left: 15px;
            padding: 8px;
        }

        .text-white {
            color: white;
        }

        .pb {
            padding-bottom: 20px;
        }

        .pb-5 {
            padding-bottom: 60px;
        }

        .pe-2 {
            padding-right: 20px;
        }

        .smart-card-header {
            border-radius: 12px 12px 0 0;
            border-style: solid;
            border-color: lightgray;
            border-bottom: none;
        }

        .pe-3 {
            padding-right: 30px;
        }

        .pe-1 {
            padding-right: 10px;
        }

        .pt-2 {
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="px-30" style="box-shadow:#64646f 0px 7px 29px 0px;width:650px;margin:auto;">
        <table class="w-100 smart-card-header"
            style="background-color: {{ $patient->SmartCardTemplate->header_color }};">
            <tbody>
                <tr>
                    <td>
                        <div class="logo me-4">
                            <img src="{{ $data['app_logo'] }}" alt="logo" class="h-100 img-fluid" />
                        </div>
                    </td>
                    <td class="">
                        <h4 class="text-white mb-0 fw-bold pb">{{ $data['app_name'] }}</h4>
                    </td>
                    <td class="text-white text-end pe-2" width="200%">
                        {{ $data['hospital_address'] }}
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="border-radius: 0 0 12px 12px; border-style: solid;border-color: lightgray;height:180px;"
            class="w-100">
            <tbody>
                <tr>
                    <td class="patient-card-body">
                        <div class="user-profile">
                            <a>
                                <div>
                                    <img src="data:image/png;base64, {{ $data['profile'] }}" alt=""
                                        height="110px" width="110px" class="user-img image"
                                        style="border-radius: 50%;">
                                </div>
                            </a>
                        </div>
                    </td>
                    <td width="100%">
                        <table class="table table-borderless patient-desc mb-0 my-3" style="margin-left: 5px;">
                            @if (!empty($patient->patientUser->full_name))
                                <tr id="cardName" class="lh-1">
                                    <td>{{ __('messages.bill.patient_name') }}:</td>
                                    <td>{{ $patient->patientUser->full_name }}</td>
                                </tr>
                            @endif
                            @if (!empty($patient->patientUser->email) && $patient->SmartCardTemplate->show_email == true)
                                <tr id="patientEmail" class="lh-1">
                                    <td>{{ __('auth.email') }}:</td>
                                    <td class="word-break">{{ $patient->patientUser->email }}</td>
                                </tr>
                            @endif
                            @if (!empty($patient->patientUser->phone) && $patient->SmartCardTemplate->show_phone == true)
                                <tr id="patientNumber" class="lh-1">
                                    <td>{{ __('messages.sms.phone_number') }}:</td>
                                    <td>{{ $patient->patientUser->region_code.$patient->patientUser->phone }}</td>
                                </tr>
                            @endif
                            @if (!empty($patient->patientUser->dob) && $patient->SmartCardTemplate->show_dob == true)
                                <tr id="patientDob" class="lh-1">
                                    <td>{{ __('messages.user.dob') }}:</td>
                                    <td>{{ $patient->patientUser->dob }}</td>
                                </tr>
                            @endif
                            @if (!empty($patient->patientUser->blood_group) && $patient->SmartCardTemplate->show_blood_group == true)
                                <tr id="patientBloodGroup" class="lh-1">
                                    <td>{{ __('messages.user.blood_group') }}:</td>
                                    <td>{{ $patient->patientUser->blood_group }}</td>
                                </tr>
                            @endif
                            @if (!empty($patient->address) && $patient->SmartCardTemplate->show_address == true)
                                <tr id="patientBloodGroup" class="lh-1">
                                    <td>{{ __('messages.common.address') }}:</td>
                                    <td class="word-break">{{ !empty($patient->address->address1) ? $patient->address->address1  : '' }}
                                        </td>
                                </tr>
                            @endif
                        </table>
                    </td>
                    <td>
                        <div class="mx-3 my-4">
                            <div class="text-center">
                                <img
                                    src="data:image/png;base64,{{ base64_encode(QrCode::size(90)->generate(route('patient.details',[$data['user_name'],$patient->patient_unique_id]))) }} ">
                            </div>
                            @if (!empty($patient->patient_unique_id) && $patient->SmartCardTemplate->show_patient_unique_id)
                                <h5 class="text-primary text-center mt-3" id="patientUniqueID">
                                    {{ $patient->patient_unique_id }}
                                </h5>
                            @endif
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
