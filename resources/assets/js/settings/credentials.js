document.addEventListener('turbo:load', loadCredentialData)

function loadCredentialData() {
    let StripeCheckbox = $('#stripeEnable').is(':checked')
    if (StripeCheckbox) {
        $('.stripe-div').removeClass('d-none')
    } else {
        $('.stripe-div').addClass('d-none')
    }

    let PaypalCheckbox = $('#paypalEnable').is(':checked')
    if (PaypalCheckbox) {
        $('.paypal-div').removeClass('d-none')
    } else {
        $('.paypal-div').addClass('d-none')
    }
    let razorpayCheckbox = $('#razorpayEnable').is(':checked')
    if (razorpayCheckbox) {
        $('.razorpay-div').removeClass('d-none')
    } else {
        $('.razorpay-div').addClass('d-none')
    }
    let paytmCheckbox = $('#paytmEnable').is(':checked')
    if (paytmCheckbox) {
        $('.paytm-div').removeClass('d-none')
    } else {
        $('.paytm-div').addClass('d-none')
    }
    let paystackCheckbox = $('#paystackEnable').is(':checked')
    if (paystackCheckbox) {
        $('.paystack-div').removeClass('d-none')
    } else {
        $('.paystack-div').addClass('d-none')
    }

    let PhonePeCheckbox = $('#phonePeEnable').is(':checked')
    if (PhonePeCheckbox) {
        $('.phonepe-div').removeClass('d-none')
    } else {
        $('.phonepe-div').addClass('d-none')
    }

    let FlutterWaveCheckbox = $('#flutterWaveEnable').is(':checked')
    if (FlutterWaveCheckbox) {
        $('.flutterWave-div').removeClass('d-none')
    } else {
        $('.flutterWave-div').addClass('d-none')
    }
}

listen('change', '#stripeEnable', function () {
    let StripeCheckbox = $('#stripeEnable').is(':checked')
    if (StripeCheckbox) {
        $('.stripe-div').removeClass('d-none')
    } else {
        $('.stripe-div').addClass('d-none')
    }
})
listen('change', '#paypalEnable', function () {
    let PaypalCheckbox = $('#paypalEnable').is(':checked')
    if (PaypalCheckbox) {
        $('.paypal-div').removeClass('d-none')
    } else {
        $('.paypal-div').addClass('d-none')
    }
})
listen('change', '#razorpayEnable', function () {
    let razorpayCheckbox = $('#razorpayEnable').is(':checked')
    if (razorpayCheckbox) {
        $('.razorpay-div').removeClass('d-none')
    } else {
        $('.razorpay-div').addClass('d-none')
    }
})
listen('change', '#paytmEnable', function () {
    let paytmCheckbox = $('#paytmEnable').is(':checked')
    if (paytmCheckbox) {
        $('.paytm-div').removeClass('d-none')
    } else {
        $('.paytm-div').addClass('d-none')
    }
})
listen('change', '#paystackEnable', function () {
    let payStackCheckbox = $('#paystackEnable').is(':checked')
    if (payStackCheckbox) {
        $('.paystack-div').removeClass('d-none')
    } else {
        $('.paystack-div').addClass('d-none')
    }
})
listen('change', '#phonePeEnable', function () {
    let PhonePeCheckbox = $('#phonePeEnable').is(':checked')
    if (PhonePeCheckbox) {
        $('.phonepe-div').removeClass('d-none');
    } else {
        $('.phonepe-div').addClass('d-none');
    }
})

listen('change', '#flutterWaveEnable', function () {
    let FlutterWaveCheckbox = $('#flutterWaveEnable').is(':checked')
    if (FlutterWaveCheckbox) {
        $('.flutterWave-div').removeClass('d-none')
    } else {
        $('.flutterWave-div').addClass('d-none')
    }
})

listenSubmit('#UserCredentialsSettings', function (e) {
    e.preventDefault()
    let StripeCheckbox = $('#stripeEnable').is(':checked')
    let PaypalCheckbox = $('#paypalEnable').is(':checked')
    let razorpayCheckbox = $('#razorpayEnable').is(':checked')
    let paytmCheckbox = $('#paytmEnable').is(':checked')
    let paystackCheckbox = $('#paystackEnable').is(':checked')
    let PhonePeCheckbox = $('#phonePeEnable').is(':checked')
    let flutterWaveCheckbox = $('#flutterWaveEnable').is(':checked')

    if (StripeCheckbox && $('#stripeKey').val().trim() == '') {
        displayErrorMessage(Lang.get('js.stripe_key'))
        return false
    }
    if (StripeCheckbox && $('#stripeSecret').val().trim() == '') {
        displayErrorMessage(Lang.get('js.stripe_secret'))
        return false
    }
    if (PaypalCheckbox && $('#paypalKey').val().trim() == '') {
        displayErrorMessage(Lang.get('js.paypal_client_id'))
        return false
    }
    if (PaypalCheckbox && $('#paypalSecret').val().trim() == '') {
        displayErrorMessage(Lang.get('js.paypal_secret'))
        return false
    }
    if (PaypalCheckbox && $('#paypalMode').val().trim() == '') {
        displayErrorMessage(Lang.get('js.paypal_mode'))
        return false
    }
    if (razorpayCheckbox && $('#razorpayKey').val().trim() == '') {
        displayErrorMessage(Lang.get('js.razorpay_key'))
        return false
    }
    if (razorpayCheckbox && $('#razorpaySecret').val().trim() == '') {
        displayErrorMessage(Lang.get('js.razor_pay_secret'))
        return false
    }
    if (paytmCheckbox && $('#paytmMerchantId').val().trim() == '') {
        displayErrorMessage(Lang.get('js.paytm_id'))
        return false
    }
    if (paytmCheckbox && $('#paytmMerchantKey').val().trim() == '') {
        displayErrorMessage(Lang.get('js.paytm_key'))
        return false
    }
    if (paystackCheckbox && $('#paystackPublicKey').val().trim() == '') {
        displayErrorMessage(Lang.get('js.paystack_key'))
        return false
    }
    if (paystackCheckbox && $('#paystackSecretKey').val().trim() == '') {
        displayErrorMessage(Lang.get('js.paystack_secret'))
        return false
    }

    if (PhonePeCheckbox && $('.phonepe_merchant_id').val().trim() == '') {
        displayErrorMessage(Lang.get('js.phonepe_merchant_id'))
        return false
    }
    if (PhonePeCheckbox && $('.phonepe_merchant_user_id').val().trim() == '') {
        displayErrorMessage(Lang.get('js.phonepe_merchant_user_id'))
        return false
    }
    if (PhonePeCheckbox && $('.phonepe_env').val().trim() == '') {
        displayErrorMessage(Lang.get('js.phonepe_env'))
        return false
    }
    if (PhonePeCheckbox && $('.phonepe_salt_key').val().trim() == '') {
        displayErrorMessage(Lang.get('js.phonepe_salt_key'))
        return false
    }
    if (PhonePeCheckbox && $('.phonepe_salt_index').val().trim() == '') {
        displayErrorMessage(Lang.get('js.phonepe_salt_index'))
        return false
    }
    if (PhonePeCheckbox && $('.phonepe_merchant_transaction_id').val().trim() == '') {
        displayErrorMessage(Lang.get('js.phonepe_merchant_transaction_id'))
        return false
    }

    if (flutterWaveCheckbox && $('.flutterwave_public_key').val().trim() == '') {
        displayErrorMessage(Lang.get('js.flutterWave_public_key'))
        return false
    }

    if (flutterWaveCheckbox && $('.flutterwave_secret_key').val().trim() == '') {
        displayErrorMessage(Lang.get('js.flutterWave_secret_key'))
        return false
    }
    $('#UserCredentialsSettings')[0].submit()
})
