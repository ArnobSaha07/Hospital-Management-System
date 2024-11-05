document.addEventListener('turbo:load', loadIpdPaymentData)

function loadIpdPaymentData () {
    if (!$('#addIpdPaymentNewForm').length &&
        !$('#editIpdPaymentForm').length) {
        return
    }

    $('#ipdPaymentDate,#editIpdPaymentDate').flatpickr({
        dateFormat: 'Y-m-d',
        enableTime: false,
        minDate: $('#showIpdPatientCaseDate').val(),
        widgetPositioning: {
            horizontal: 'right',
            vertical: 'bottom',
        },
        locale: $('.userCurrentLanguage').val(),
    })

    $('#ipdPaymentModeId').select2({
        width: '100%',
        dropdownParent: $('#addIpdPaymentModal'),
    })
    $('#editIpdPaymentModeId').select2({
        width: '100%',
        dropdownParent: $('#editIpdPaymentModal'),
    })
}

listen('click', '.ipdpayment-delete-btn', function (event) {
    let id = $(event.currentTarget).attr('data-id')
    deleteItem($('#showIpdPaymentUrl').val() + '/' + id, null,
        $('#ipdPaymentLang').val())
})

listen('click', '.ipdpayment-edit-btn', function (event) {
    if ($('.ajaxCallIsRunning').val()) {
        return
    }
    ajaxCallInProgress()
    let ipdPaymentId = $(event.currentTarget).attr('data-id')
    renderIpdPaymentData(ipdPaymentId)
})

listenSubmit('#addIpdPaymentNewForm', function (event) {
    event.preventDefault()
    let loadingButton = jQuery(this).find('#btnIpdPaymentSave')
    loadingButton.button('loading')

    var formData = new FormData($(this)[0])
    $.ajax({
        url: $('#showIpdPaymentCreateUrl').val(),
        type: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function success (result) {
            if (result.success) {
                if(result.data == null){
                    displaySuccessMessage(result.message);
                    $("#addIpdPaymentModal").modal("hide");
                    Livewire.dispatch('refresh')
                }else{
                    if(result.data.payment_type == '3'){
                        let payloadData = {
                            amount: result.data.amount,
                            ipdNumber: result.data.ipdID,
                            notes : result.data.notes
                        };
                        let stripeKey = $('#stripeConfigKey').val();
                        let stripe = Stripe(stripeKey);

                        $(this).html(
                            '<div class="spinner-border spinner-border-sm " role="status">\n' +
                            '                                            <span class="sr-only">Loading...</span>\n' +
                            '                                        </div>').addClass('disabled');
                        $.post($('#showListIpdStripePaymentUrl').val(), payloadData).done((res) => {
                            let sessionId = res.data.sessionId;
                            stripe.redirectToCheckout({
                                sessionId: sessionId,
                            }).then(function (res) {
                                manageAjaxErrors(res);
                            });
                        }).catch(error => {
                            manageAjaxErrors(error);
                        });
                    }
                    if(result.data.payment_type == '5'){
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
                                'notes' : '',
                            },
                            'readonly': {
                                'name': 'true',
                                'email': 'true',
                                'contact': 'true',
                                'notes' : 'true',
                            },
                            'modal': {
                                'ondismiss': function () {
                                    displayErrorMessage(Lang.get('js.your_payment_failed'))
                                },
                            },
                        }
                        $.ajax({
                            type: 'GET',
                            url: route('patient.razorpay.init'),
                            data: {
                                'amount': result.data.amount,
                                'ipdNumber': result.data.ipdID,
                                'notes' : result.data.notes
                            },
                            success: function (res) {
                                if (res.url) {
                                    window.location.href = res.url
                                }
                                if (res.success) {
                                    let {
                                        id,
                                        currency,
                                        amount,
                                        name,
                                        email,
                                        contact,
                                        notes,
                                    } = res.data
                                    patientOptions.currency = currency
                                    patientOptions.amount = amount
                                    patientOptions.order_id = id
                                    patientOptions.prefill.name = name
                                    patientOptions.prefill.email = email
                                    patientOptions.prefill.contact = contact
                                    patientOptions.prefill.notes = notes
                                    let patientRazorPay = new window.Razorpay(patientOptions)
                                    patientRazorPay.open()
                                    patientRazorPay.on('payment.failed', storePatientFailedPayment)
                                }
                            },
                            error: function (res) {
                                displayErrorMessage(res.responseJSON.message)
                            },
                            complete: function () {
                            },
                        });
                    }
                    if(result.data.payment_type == '4'){
                        $.ajax({
                            type: 'GET',
                            url: route('patient.paypal.init'),
                            data: {
                                'amount': result.data.amount,
                                'ipdId': result.data.ipdID,
                                'notes' : result.data.notes
                            },
                            success: function (res) {
                                if (res.url) {
                                    window.location.href = res.url
                                }
                            },
                            error: function (res) {
                                displayErrorMessage(res.responseJSON.message)
                            },
                            complete: function () {
                            },
                        });
                    }
                    if(result.data.payment_type == 6){
                        window.location.replace(
                            route('patient.paytm.init', {
                                'amount': result.data.amount,
                                'ipdNumber': result.data.ipdID,
                            })
                        )
                    }
                    if(result.data.payment_type == 7){
                        window.location.replace(route('ipd.paystack.init', {
                            'amount': result.data.amount,
                            'ipdNumber': result.data.ipdID,
                            'notes' : result.data.notes
                        }));
                    }
                    if(result.data.payment_type == '8'){
                       window.location.href = result.data.url;
                    }
                    if(result.data.payment_type == '9'){
                        window.location.href = result.data.url;
                     }
                }
            }
        },
        error: function error (result) {
            printErrorMessage('#ipdPaymentValidationErrorsBox', result)
        },
        complete: function complete () {
            loadingButton.button('reset')
        },
    })

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

function renderIpdPaymentData (id) {
    $.ajax({
        url: $('#showIpdPaymentUrl').val() + '/' + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let ext = result.data.ipd_payment_document_url.split('.').
                    pop().
                    toLowerCase()
                if (ext == 'pdf') {
                    $('#editIpdPaymentPreviewImage').
                        css('background-image',
                            'url("' + $('.pdfDocumentImageUrl').val() + '")')
                } else if ((ext == 'docx') || (ext == 'doc')) {
                    $('#editIpdPaymentPreviewImage').
                        css('background-image',
                            'url("' + $('.docxDocumentImageUrl').val() + '")')
                } else {
                    if (result.data.ipd_payment_document_url != '') {
                        $('#editIpdPaymentPreviewImage').
                            css('background-image',
                                'url("' + result.data.ipd_payment_document_url +
                                '")')
                    }
                }
                $('#ipdPaymentId').val(result.data.id)
                $('#editIpdPaymentAmount').val(result.data.amount)
                document.querySelector('#editIpdPaymentDate').
                    _flatpickr.
                    setDate(
                        moment(result.data.date).format('YYYY-MM-DD h:mm A'))
                $('#editIpdPaymentNote').val(result.data.notes)
                $('#editIpdPaymentModeId').
                    val(result.data.payment_mode).
                    trigger('change.select2')
                $('#editIpdPaymentModal').modal('show')
                ajaxCallCompleted()
            }
        },
        error: function (result) {
            manageAjaxErrors(result)
        },
    })
}

listenSubmit('#editIpdPaymentForm', function (event) {
    event.preventDefault()
    let loadingButton = jQuery(this).find('#btnEditIpdPaymentSave')
    loadingButton.button('loading')
    let id = $('#ipdPaymentId').val()
    let url = $('#showIpdPaymentUrl').val() + '/' + id
    let data = {
        'formSelector': $(this),
        'url': url,
        'type': 'POST',
        // 'tableSelector': tableName,
    }
    editIpdPaymentRecord(data, loadingButton, '#editIpdPaymentModal')
})

listenHiddenBsModal('#addIpdPaymentModal', function () {
    resetModalForm('#addIpdPaymentNewForm', '#ipdPaymentValidationErrorsBox')
    $('#ipdPaymentPreviewImage').
        attr('src', $('#showDefaultDocumentImageUrl').val())
    $('#ipdPaymentPreviewImage').
        css('background-image',
            'url("' + $('#showDefaultDocumentImageUrl').val() + '")')
})

listenHiddenBsModal('#editIpdPaymentModal', function () {
    resetModalForm('#editIpdPaymentForm',
        '#editIpdPaymentValidationErrorsBox')
})

listenChange('#ipdPaymentDocumentImage', function () {
    let extension = isValidIpdPaymentDocument($(this),
        '#ipdPaymentValidationErrorsBox')
    if (!isEmpty(extension) && extension != false) {
        $('#ipdPaymentValidationErrorsBox').html('').hide()
        displayDocument(this, '#ipdPaymentPreviewImage', extension)
    }
})

listenChange('#editIpdPaymentDocumentImage', function () {
    let extension = isValidIpdPaymentDocument($(this),
        '#editIpdPaymentValidationErrorsBox')
    if (!isEmpty(extension) && extension != false) {
        $('#editIpdPaymentValidationErrorsBox').html('').hide()
        displayDocument(this, '#editIpdPaymentPreviewImage', extension)
    }
})

function isValidIpdPaymentDocument (inputSelector, validationMessageSelector) {
    let ext = $(inputSelector).val().split('.').pop().toLowerCase()
    if ($.inArray(ext, ['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx']) == -1) {
        $(inputSelector).val('')
        $(validationMessageSelector).
            html(
                Lang.get('js.document_must_be_file_type')).
            show()
        return false
    }
    return ext
}

function deleteItemPaymentAjax (url, tableId, header, callFunction = null) {
    $.ajax({
        url: url,
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                Livewire.dispatch('resetPage')
            }
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                confirmButtonColor: '#009ef7',
                text: header +' '+Lang.get('js.has_been_deleted'),
                timer: 2000,
            })
            if (callFunction) {
                eval(callFunction);
            }
        },
        error: function (data) {
            Swal.fire({
                title: '',
                text: data.responseJSON.message,
                confirmButtonColor: '#009ef7',
                icon: 'error',
                timer: 5000,
            })
        },
    });
}

window.editIpdPaymentRecord = function (data, loadingButton) {
    var modalSelector = arguments.length > 2 && arguments[2] !== undefined
        ? arguments[2]
        : '#EditModal';
    var formData = data.formSelector === '' ? data.formData : new FormData(
        $(data.formSelector)[0]);
    $.ajax({
        url: data.url,
        type: data.type,
        data: formData,
        processData: false,
        contentType: false,
        success: function success (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $(modalSelector).modal('hide');
                // $(tableName).DataTable().ajax.reload(null, true);
                Livewire.dispatch('refresh')
            }
        },
        error: function error (result) {
            UnprocessableInputError(result)
        },
        complete: function complete () {
            loadingButton.button('reset')
        },
    });
};

listen('click', '#ipdPaymentDocumentImage', function () {
    defaultImagePreview('#ipdPaymentPreviewImage')
})

listen('click', '.removeIpdPaymentImageEdit', function () {
    defaultImagePreview('#editIpdPaymentPreviewImage')
})

listenChange("#ipdPaymentModeId", function () {
    let payment_mode = $(this).val();

    if(payment_mode == '3' || payment_mode == '5' ||  payment_mode == '4' || payment_mode == '7' || payment_mode == '8' || payment_mode == '9'){
        $('.ipd_payment_document').addClass('d-none');
    }
    else{
        $('.ipd_payment_document').removeClass('d-none');
    }
    if(payment_mode == '4'){
        $('.ipd_payment_notes').addClass('d-none');
    }
    else{
        $('.ipd_payment_notes').removeClass('d-none');
    }
});
