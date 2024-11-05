document.addEventListener('turbo:load', loadIpdPaymentsData)

function loadIpdPaymentsData() {

    if($('#showListIpdTimelinesUrl').length){

        let patientOptions = {
            'key': $('.patientRazorpayDataKey').val(),
            'amount': 0, //  100 refers to 1
            'currency': 'INR',
            'name': $('.patientRazorpayDataName').val(),
            'order_id': '',
            'description': '',
            'image': $('.patientRazorpayDataImage').val(), // logo here
            'callback_url': $('.patientRazorpayDataCallBackURL').val(),
            'prefill': {
                'email': '', // recipient email here
                'name': '', // recipient name here
                'contact': '', // recipient phone here
            },
            'readonly': {
                'name': 'true',
                'email': 'true',
                'contact': 'true',
            },
            'modal': {
                'ondismiss': function () {
                    displayErrorMessage(Lang.get('js.your_payment_failed'))
                },
            },
        }

        listenClick('#ipdPaymentRazorpayBtn', function () {
            $.ajax({
                type: 'GET',
                url: route('patient.razorpay.init'),
                data: {
                    'amount': parseInt($('#billAmout').val()),
                    'ipdNumber': $('#ipdNumber').val(),
                },
                success: function (result) {
                    if (result.url) {
                        window.location.href = result.url
                    }
                    if (result.success) {
                        let {
                            id,
                            currency,
                            amount,
                            name,
                            email,
                            contact,
                        } = result.data
                        patientOptions.currency = currency
                        patientOptions.amount = amount
                        patientOptions.order_id = id
                        patientOptions.prefill.name = name
                        patientOptions.prefill.email = email
                        patientOptions.prefill.contact = contact
                        let patientRazorPay = new Razorpay(patientOptions)
                        patientRazorPay.open()
                        patientRazorPay.on('payment.failed', storePatientFailedPayment)
                    }
                },
                error: function (result) {
                    displayErrorMessage(result.responseJSON.message)
                },
                complete: function () {
                },
            });
        });

    }
}

listenClick('#ipdPaymentPaypalBtn', function () {
    $.ajax({
        type: 'GET',
        url: route('patient.paypal.init'),
        data: {
            'amount': parseInt($('#billAmout').val()),
            'ipdNumber': $('#ipdNumber').val(),
        },
        success: function (result) {
            if (result.url) {
                window.location.href = result.url
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
        complete: function () {
        },
    });
});


function storePatientFailedPayment (response) {
    $.ajax({
        type: 'POST',
        url: $('.patientRazorpayPaymentFailed').val(),
        data: {
            data: response,
        },
        success: function (result) {
            if (result.url) {
                window.location.href = result.url
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
    })
}

listenClick('#ipdPaymentPaytmBtn', function () {
    window.location.replace(
        route('patient.paytm.init', {
            'amount': parseInt($('#billAmout').val()),
            'ipdNumber': $('#ipdNumber').val(),
        })
    )
})

listenClick('#ipdPaymentPayStackBtn', function () {
    window.location.replace(route('patient.paystack.init', {
        'amount': parseInt($('#billAmout').val()),
        'ipdNumber': $('#ipdNumber').val(),
    }));
})
