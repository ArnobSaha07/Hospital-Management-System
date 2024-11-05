@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ asset(getSuperAdminAppLogoUrl()) }}" class="logo" style="object-fit: cover" alt="{{ getSuperAdminAppName() }}">
        @endcomponent
    @endslot
    {{-- Body --}}
    <div>
        <h2>Dear {{ $patient_name }}, <b></b></h2><br>
        <p>I hope you are well.</p>
        <p>Below are your invoice details.</p>
        <p><b>Invoice Number:</b> {{$invoice_number}}</p>
        <p><b>Invoice Date:</b> {{$invoice_date}}</p>
        <p><b>Discount:</b> {{$discount}}</p>
        <p><b>Amount:</b> {{$amount}}</p>
        <p><b>Status:</b> {{$status}}</p>
        <p>Please see attached the invoice <b>#{{ $invoice_number }}</b>.</p>
        <p>Also you can see the attachment invoice PDF.</p><br>
        <div style="display: flex;justify-content: center">
            <a href="{{url('employee/invoices'). '/' . $invoice_id}}"
               style="padding: 7px 15px;text-decoration: none;font-size: 14px;background-color: #FFC300;font-weight: 500;border: none;border-radius: 8px;color: white">
                View Invoice
            </a>
        </div>
    </div>
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <h6>© {{ date('Y') }} {{ getSuperAdminAppName() }}.</h6>
        @endcomponent
    @endslot
@endcomponent
