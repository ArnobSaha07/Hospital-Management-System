document.addEventListener("turbo:load", loadSubscriptionData);

function loadSubscriptionData() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    if ($("#paymentType").val() == "") {
        $(
            ".paypalPayment, .razor_pay_payment, .cash_payment, .paymentByPaytm, .paymentByPaystack"
        ).attr("disabled", true);
    }
}

function paymentMessage(data = null) {
    // toastData = data != null ? data : toastData;
    toastData = data;
    if (toastData !== null) {
        setTimeout(function () {
            $.toast({
                heading: toastData.toastType,
                icon: toastData.toastType,
                bgColor: "danger",
                textColor: "#ffffff",
                text: toastData.toastMessage,
                position: "top-right",
                stack: false,
            });
        }, 1000);
    }
}

listenClick(".makePayment", function () {
    if (
        typeof $(".getLoggedInUserdata").val() != "undefined" &&
        $(".getLoggedInUserdata").val() == ""
    ) {
        window.location.href = $(".logInUrl").val();

        return true;
    }

    let payloadData = {
        plan_id: $(this).data("id"),
        from_pricing:
            typeof $(".fromPricing").val() != "undefined"
                ? $(".fromPricing").val()
                : null,
        price: $(this).data("plan-price"),
        payment_type: $("#paymentType option:selected").val(),
    };
    $(this).addClass("disabled");
    $.post($(".makePaymentURL").val(), payloadData)
        .done((result) => {
            if (typeof result.data == "undefined") {
                let toastMessageData = {
                    toastType: "success",
                    toastMessage: result.message,
                };
                paymentMessage(toastMessageData);
                setTimeout(function () {
                    window.location.href = $(".subscriptionPlans").val();
                }, 5000);

                return true;
            }

            // let stripe = $('.stripe').val()
            let sessionId = result.data.sessionId;
            stripe
                .redirectToCheckout({
                    sessionId: sessionId,
                })
                .then(function (result) {
                    $(this)
                        .html($(".subscribeText").val())
                        .removeClass("disabled");
                    $(".makePayment").attr("disabled", false);
                    let toastMessageData = {
                        toastType: "error",
                        toastMessage: result.responseJSON.message,
                    };
                    paymentMessage(toastMessageData);
                });
        })
        .catch((error) => {
            $(this).html($(".subscribeText").val()).removeClass("disabled");
            $(".makePayment").attr("disabled", false);
            let toastMessageData = {
                toastType: "error",
                toastMessage: error.responseJSON.message,
            };
            paymentMessage(toastMessageData);
        });
});

listenChange("#paymentType", function () {
    let paymentType = $(this).val();
    if (paymentType == "") {
        $(
            ".makePayment,.paymentByPaypal, .razor_pay_payment, .cash_payment, .paymentByPaytm, .paymentByPaystack, .phonePePayment, .flutterWavePayment"
        ).attr("disabled", true);
    }
    if (paymentType == 1) {
        $(
            ".proceed-to-payment, .razorPayPayment, .cashPayment, .paytmPayment, .paystackPayment,.manuallyPayAttachment, .phonePePayment, .flutterWavePayment"
        ).addClass("d-none");
        $(".stripePayment").removeClass("d-none");
        $(".makePayment").attr("disabled", false);
    }
    if (paymentType == 2) {
        $(
            ".proceed-to-payment, .razorPayPayment, .cashPayment, .paytmPayment, .paystackPayment,.manuallyPayAttachment, .phonePePayment, .flutterWavePayment"
        ).addClass("d-none");
        $(".paypalPayment").removeClass("d-none");
        $(".paymentByPaypal").attr("disabled", false);
    }
    if (paymentType == 3) {
        $(
            ".proceed-to-payment, .paypalPayment, .cashPayment, .paytmPayment, .paystackPayment,.manuallyPayAttachment, .phonePePayment, .flutterWavePayment"
        ).addClass("d-none");
        $(".razorPayPayment").removeClass("d-none");
        $(".razor_pay_payment").attr("disabled", false);
    }
    if (paymentType == 4) {
        $(
            ".proceed-to-payment, .paypalPayment, .razorPayPayment, .paytmPayment, .paystackPayment, .phonePePayment, .flutterWavePayment"
        ).addClass("d-none");
        $(".cashPayment").removeClass("d-none");
        $(".manuallyPayAttachment").removeClass("d-none");
        $(".cash_payment").attr("disabled", false);
        if ($(".manual-intro").val() != null) {
            $(".manualIntro").removeClass("d-none");
        } else {
            $(".manualIntro").addClass("d-none");
        }
    }
    if (paymentType == 5) {
        $(
            ".proceed-to-payment, .paypalPayment, .razorPayPayment, .cashPayment, .paystackPayment,.manuallyPayAttachment, .flutterWavePayment"
        ).addClass("d-none");
        $(".paytmPayment").removeClass("d-none");
        $(".paymentByPaytm").attr("disabled", false);
    }
    if (paymentType == 6) {
        $(
            ".proceed-to-payment, .paypalPayment, .razorPayPayment, .cashPayment, .paytmPayment,.manuallyPayAttachment, .phonePePayment, .flutterWavePayment"
        ).addClass("d-none");
        $(".paystackPayment").removeClass("d-none");
        $(".paymentByPaystack").attr("disabled", false);
    }
    if (paymentType == 7) {
        $(
            ".proceed-to-payment, .paypalPayment, .razorPayPayment, .cashPayment, .paytmPayment,.manuallyPayAttachment, .paystackPayment, .flutterWavePayment"
        ).addClass("d-none");
        $(".phonePePayment").removeClass("d-none");
        $(".paymentByPhonePe").attr("disabled", false);
    }
    if (paymentType == 8) {
        $(
            ".proceed-to-payment, .paypalPayment, .razorPayPayment, .cashPayment, .paytmPayment,.manuallyPayAttachment, .paystackPayment, .phonePePayment"
        ).addClass("d-none");
        $(".flutterWavePayment").removeClass("d-none");
        $(".paymentByflutterWave").attr("disabled", false);
    }
});

listenClick(".paymentByPaypal", function () {
    let pricing =
        typeof $(".fromPricing").val() != "undefined"
            ? $(".fromPricing").val()
            : null;
    $(this).addClass("disabled");
    $.ajax({
        type: "GET",
        url: route("paypal.init"),
        data: {
            planId: $(this).data("id"),
            from_pricing: pricing,
            payment_type: $("#paymentType option:selected").val(),
        },
        success: function (result) {
            if (result.success) {
                let toastMessageData = {
                    toastType: "success",
                    toastMessage: result.message,
                };
                paymentMessage(toastMessageData);
                setTimeout(function () {
                    window.location.href = $(".subscriptionPlans").val();
                }, 5000);

                return true;
            }

            if (result.url) {
                window.location.href = result.url;
            }

            if (result.status == 201) {
                let redirectTo = "";

                location.href = result.link;

                // $.each(result.result.links,
                //     function (key, val) {
                //         if (val.rel == 'approve') {
                //             redirectTo = val.href
                //         }
                //     })
                // location.href = redirectTo
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {},
    });
});

listenClick(".paymentByPhonePe", function () {
    let pricing =
        typeof $(".fromPricing").val() != "undefined"
            ? $(".fromPricing").val()
            : null;
    $(this).addClass("disabled");
    $.ajax({
        type: "GET",
        url: route("subscription.phonepe.init"),
        data: {
            planId: $(this).data("id"),
            from_pricing: pricing,
        },
        success: function (result) {
            if (result.data.url != null) {
                window.location.href = result.data.url;
            }
        },
        error: function (result) {
            $(".ajax-message").css("display", "block");
            $(".ajax-message")
                .html(
                    '<div class="err alert alert-danger">' +
                        result.responseJSON.message +
                        "</div>"
                )
                .delay(5000)
                .hide("slow");
            displayErrorMessage(result.responseJSON.message);
            $(this).removeClass("disabled");
        },
    });
});

listenClick(".paymentByPaytm", function () {
    let pricing =
        typeof $(".fromPricing").val() != "undefined"
            ? $(".fromPricing").val()
            : null;
    window.location.replace(
        route("paytm.init", {
            planId: $(this).data("id"),
            from_pricing: pricing,
        })
    );
});

let options = {
    key: $(".razorpayDataKey").val(),
    amount: 1, //  100 refers to 1
    currency: "INR",
    name: $(".razorpayDataName").val(),
    order_id: "",
    description: "",
    image: $(".razorpayDataImage").val(), // logo here
    callback_url: route("razorpay.success"),
    prefill: {
        email: "", // recipient email here
        name: "", // recipient name here
        contact: "", // recipient phone here
    },
    readonly: {
        name: "true",
        email: "true",
        contact: "true",
    },
    modal: {
        ondismiss: function () {
            $.ajax({
                type: "POST",
                url: $(".razorpayPaymentFailed").val(),
                success: function (result) {
                    if (result.url) {
                        displayErrorMessage("Payment not completed.");
                        setTimeout(function () {
                            window.location.href = result.url;
                        }, 3000);
                    }
                },
                error: function (result) {
                    displayErrorMessage(result.responseJSON.message);
                },
            });
        },
    },
};

listenClick(".razor_pay_payment", function () {
    $(this).addClass("disabled");
    $.ajax({
        type: "POST",
        url: $(".makeRazorpayURl").val(),
        data: {
            plan_id: $(this).data("id"),
            from_pricing:
                typeof $(".fromPricing").val() != "undefined"
                    ? $(".fromPricing").val()
                    : null,
        },
        success: function (result) {
            if (result.url) {
                window.location.href = result.url;
            }
            if (result.success) {
                let { id, amount, name, email, contact, planID } = result.data;
                options.amount = amount;
                options.order_id = id;
                options.prefill.name = name;
                options.prefill.email = email;
                options.prefill.contact = contact;
                options.prefill.planID = planID;
                let razorPay = new Razorpay(options);
                razorPay.open();
                razorPay.on("payment.failed", storeFailedPayment);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {},
    });
});

function storeFailedPayment(response) {
    $.ajax({
        type: "POST",
        url: $(".razorpayPaymentFailed").val(),
        data: {
            data: response,
        },
        success: function (result) {
            if (result.url) {
                window.location.href = result.url;
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
}

listenClick(".cash_payment", function () {
    $(this).addClass("disabled");

    let formData = new FormData($(".manuallyPaymentForm")[0]);
    $.ajax({
        type: "POST",
        url: $(".cashPaymentUrl").val(),
        // data: new FormData({
        //     'plan_id': $(this).data('id'),
        //     'from_pricing': typeof $('.fromPricing').val() != 'undefined'
        //         ? $('.fromPricing').val()
        //         : null,
        //     'notes': $('.notes').val(),
        // }),
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.url) {
                window.location.href = result.url;
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {},
    });
});

listenClick(".paymentByPaystack", function () {
    let pricing =
        typeof $(".fromPricing").val() != "undefined"
            ? $(".fromPricing").val()
            : null;
    window.location.replace(
        route("paystack.init", {
            planId: $(this).data("id"),
            from_pricing: pricing,
        })
    );
});

listenClick("#resetSubscriptionFilter", function () {
    $("#subscriptionStatusFilter").val("").trigger("change");
    $("#subscriptionExpireStatusFilter").val("").trigger("change");
    hideDropdownManually($("#subscriptionFilter"), $(".dropdown-menu"));
});

listenChange("#subscriptionStatusFilter", function () {
    Livewire.dispatch("changeFilter", { statusFilter: $(this).val() });
});

listenChange("#subscriptionExpireStatusFilter", function () {
    Livewire.dispatch("changeExpireFilter", {
        expireStatusFilter: $(this).val(),
    });
});

listenClick(".paymentByflutterWave", function () {
    $(this).addClass("disabled");
    $.ajax({
        type: "GET",
        url: route("purchase.subscription.flutterwave"),
        data: {
            plan_id: $(this).data("id"),
            from_pricing:
                typeof $(".fromPricing").val() != "undefined"
                    ? $(".fromPricing").val()
                    : null,
        },
        success: function (result) {
            if (result.data.url != null) {
                window.location.href = result.data.url;
            }
        },
        error: function (result) {
            $(".ajax-message").css("display", "block");
            $(".ajax-message")
                .html(
                    '<div class="err alert alert-danger">' +
                        result.responseJSON.message +
                        "</div>"
                )
                .delay(5000)
                .hide("slow");
            displayErrorMessage(result.responseJSON.message);
            $(this).removeClass("disabled");
        },
    });
});
