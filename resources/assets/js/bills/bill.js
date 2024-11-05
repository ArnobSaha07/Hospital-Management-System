'use strict'

Livewire.hook("element.init", () => {
    $(".make-bill-payment").select2({
        width: "100%",
    });

});

listenClick('.bill-delete-btn', function (event) {
    let id = $(event.currentTarget).data('id')
    deleteItem($('#indexBillUrl').val() + '/' + id, '', $('#billLang').val())
})

listenChange('.make-bill-payment', function (event){
    console.log($('.bill-amount').val());
    event.preventDefault();
    swal({
        title: Lang.get("js.are_u_sure"),
        text: Lang.get("js.u_want_to_complete_this_payment"),
        icon: "warning",
        buttons: {
            confirm: $(".yesVariable").val(),
            cancel: $(".noVariable").val() + ", " + $(".cancelVariable").val(),
        },
    }).then((result) => {
        if (result) {
            let billID = $(this).attr('data-id');
            let paymentID = $(this).val();
            $.ajax({
                url: route('bill.payment', billID),
                method: 'post',
                cache: false,
                data:{
                    // amount:$('.bill-amount').val(),
                    id:billID,
                    paymentType:paymentID,
                },
                success: function (data) {
                    if (data.data == null) {
                        displaySuccessMessage(data.message)
                        Livewire.dispatch('refresh')
                    }else{
                        if (data.data.payment_type == 0) {
                            let stripe = Stripe(data.data.stripe_key);
                            let sessionId = data.data[0].sessionId;
                            stripe.redirectToCheckout({
                                sessionId: sessionId,
                            })
                            .then(mainResult => manageAjaxErrors(mainResult));
                        }
                        if (data.data.payment_type == 1) {
                            window.location.href = data.data.url;
                        }
                        if (data.data.payment_type == 3) {
                            window.location.href = data.data.url;
                        }
                    }
                },
                error: function (result) {
                    displayErrorMessage(result.responseJSON.message)
                },
            });
            // $.ajax({
            //     url: route("manual-billing-payments.store"),
            //     type: "POST",
            //     data: { id: id, payment_type: payment_type },
            //     success: function (data) {
            //         if (data.data == null) {
            //             displaySuccessMessage(data.message);
            //             Livewire.dispatch("refresh");
            //         }else{
            //             if (data.data.payment_type == "0") {
            //                 let sessionId = data.data[0].sessionId;
            //                 stripe.redirectToCheckout({
            //                     sessionId: sessionId,
            //                 })
            //                 .then(mainResult => manageAjaxErrors(mainResult));
            //             }
            //         }
            //     },
            // });
        } else {
            Livewire.dispatch("refresh");
        }
    });
    // if(paymentID == 1){

    // }
    // if(paymentID == 2){
    //     $.ajax({
    //         url: route('bill.payment', billID),
    //         method: 'post',
    //         cache: false,
    //         data:{
    //             amount:$('.bill-amount').val(),
    //             id:billID,
    //             paymentType:paymentID,
    //         },
    //         success: function (result) {
    //             if (result.success) {
    //                 displaySuccessMessage(result.message)
    //                 Livewire.dispatch('refresh')
    //             }
    //         },
    //         error: function (result) {
    //             displayErrorMessage(result.responseJSON.message)
    //         },
    //     });
    // }
});

listenChange('.bill-payment-approve', function () {
    let id = $(this).attr('data-id');
    let status = $(this).val();

    $.ajax({
        url: route('change-bill-payment-status', id),
        type: 'GET',
        data: { id: id, status: status },
        success: function (result) {
            displaySuccessMessage(result.message);
            Livewire.dispatch('refresh')
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});
