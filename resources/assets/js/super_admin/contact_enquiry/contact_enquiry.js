document.addEventListener("turbo:load", loadContactEnquiryData);

("use strict");

function loadContactEnquiryData() {

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // function onloadCallback() {
    //     if ($('#g-recaptcha').length) {
    //         grecaptcha.render('g-recaptcha', {
    //             'sitekey': $('#superAdminEnquiryGRecaptcha').val(),
    //         })
    //     }
    // }

    if ($('#superAdminContactEnquiryForm').length) {
        listenSubmit('#superAdminContactEnquiryForm', function (e) {
            e.preventDefault()
            if ($('.error-msg').text() !== '') {
                $('.phoneNumber').focus()
                return false
            }
            $('.ajax-message-contact').css('display', 'block')
            $('.ajax-message-contact').html('')
            $.ajax({
                url: $("#superAdminEnquiryStore").val(),
                type: "POST",
                data: $(this).serialize(),
                success: function (result) {
                    if (result.success) {
                        $('.ajax-message-contact').
                            html('<div class="gen alert alert-success">' +
                                result.message + '</div>').
                            delay(5000).
                            hide('slow')
                            if ((typeof $('.isAdminGoogleCaptchaEnabled').val() == undefined)
                    ? ''
                    : $('.isAdminGoogleCaptchaEnabled').val()) {
                    grecaptcha.reset();
                }
                    $('.error-msg').addClass('d-none')
                    $('.valid-msg').addClass('d-none')
                        $('#superAdminContactEnquiryForm')[0].reset()
                        if($('#superAdminCaptchaSetting').val()){
                            grecaptcha.reset();
                        }
                    } else {
                        $(".ajax-message-contact")
                            .html(
                                '<div class="gen alert alert-danger">' +
                                    result.message +
                                    "</div>"
                            )
                            .delay(5000)
                            .hide("slow");
                    }
                    if ($("#superAdminCaptchaSetting").val()) {
                        grecaptcha.reset();
                    }
                },
                error: function (result) {
                    $(".ajax-message-contact")
                        .html(
                            '<div class="err alert alert-danger">' +
                                result.responseJSON.message +
                                "</div>"
                        )
                        .delay(5000)
                        .hide("slow");
                    if ($("#superAdminCaptchaSetting").val()) {
                        grecaptcha.reset();
                    }
                    $("#superAdminContactEnquiryForm")[0].reset();
                },
            });
        });
    } else {
        return false;
    }
}
