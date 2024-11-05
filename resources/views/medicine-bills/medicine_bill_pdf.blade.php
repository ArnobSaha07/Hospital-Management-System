<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "//www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="icon" href="{{ asset('web/img/hms-saas-favicon.ico') }}" type="image/png">
    <title>{{ __('messages.medicine_bills.medicine_bill') }}</title>
    <link href="{{ asset('assets/css/bill-pdf.css') }}" rel="stylesheet" type="text/css"/>
    @if(getCurrentCurrency() == 'inr')
        <style>
            body {
                font-family: DejaVu Sans, sans-serif !important;
            }
        </style>
    @endif
    <style>
        * {
            font-family: DejaVu Sans, Arial, "Helvetica", Arial, "Liberation Sans", sans-serif;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td class="header-left">
                <div class="main-heading">{{ __('messages.medicine_bills.medicine_bill') }}</div>
            </td>
            <td class="header-right">
                <div class="logo"><img width="100px" src="{{ $data['app_logo'] }}" alt=""></div>
                <div class="hospital-name">{{ $data['app_name'] }}</div>
                <div class="hospital-name font-color-gray">{{ $data['hospital_address'] }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="address">
                    <tr>
                        <td colspan="2">
                            <span class="font-weight-bold patient-detail-heading">{{ __('messages.bill.bill_id') }}:</span>
                            #{{ $medicineBill->bill_number }}
                            <br>
                            <span class="font-weight-bold patient-detail-heading">{{ __('messages.bill.bill_date') }}:</span>
                            {{ \Carbon\Carbon::parse($medicineBill->bill_date)->format('jS M,Y g:i A') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"
                            class="font-weight-bold patient-detail-heading">{{ __('messages.patient.patient_details') }}</td>
                    </tr>
                    <tr>
                        <td class="patient-details">
                            <table class="patient-detail-one">
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.investigation_report.patient') }}:</td>
                                    <td>{{ $medicineBill->patient->user->full_name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.user.email') }}:</td>
                                    <td>{{ $medicineBill->patient->user->email }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.bill.cell_no') }}:</td>
                                    <td>{{ !empty($medicineBill->patient->user->phone) ? $medicineBill->patient->user->region_code.$medicineBill->patient->user->phone : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.user.gender') }}:</td>
                                    <td>{{ $medicineBill->patient->user->gender == 0 ? __('messages.user.male') : __('messages.user.female') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ __('messages.user.dob') }}:</td>
                                    <td>{{ !empty($medicineBill->patient->user->dob) ? Datetime::createFromFormat('Y-m-d',  $medicineBill->patient->user->dob)->format('jS M, Y g:i A') : 'N/A' }}</td>
                                </tr>
                                @if (!empty($medicineBill->docto))
                                    <tr>
                                        <td class="font-weight-bold">{{ __('messages.investigation_report.doctor') }}:</td>
                                        <td>{{ $medicineBill->doctor->user->full_name }}</td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="items-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.bill.item_name') }}</th>
                        <th class="number-align">{{ __('messages.bill.qty') }}</th>
                        <th class="number-align">{{ __('messages.bill.price') }}</th>
                        <th class="number-align">{{ __('messages.purchase_medicine.tax') }}</th>
                        <th class="number-align">{{ __('messages.bill.amount') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($medicineBill->saleMedicine) && !empty($medicineBill->saleMedicine))
                        @foreach ($medicineBill->saleMedicine as $index => $saleMedicine)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $saleMedicine->medicine->name }}
                                </td>
                                <td class="number-align">{{ $saleMedicine->sale_quantity }}</td>
                                <td class="number-align">
                                    {{ getCurrencyFormat($saleMedicine->sale_price) }}</td>
                                <td class="number-align">
                                    {{ $saleMedicine->tax . '%' }}</td>
                                <td class="number-align">
                                    {{ getCurrencyFormat($saleMedicine->sale_price * $saleMedicine->sale_quantity) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <table class="bill-footer">
                    <tr>
                        <td class="font-weight-bold">{{ __('messages.purchase_medicine.total') . ':' }}</td>
                        <td class="text-gray-900 text-end pe-0">
                            {{ number_format($medicineBill->total, 2) }} </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">{{ __('messages.purchase_medicine.tax') . ':' }}</td>
                        <td class="text-gray-900 text-end pe-0">
                            {{ number_format($medicineBill->tax_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">{{ __('messages.purchase_medicine.discount') . ':' }}</td>
                        <td class="text-gray-900 text-end pe-0">
                            {{ number_format($medicineBill->discount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">{{ __('messages.purchase_medicine.net_amount') . ':' }}</td>
                        <td class="text-gray-900 text-end pe-0">
                            {{ number_format($medicineBill->net_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
