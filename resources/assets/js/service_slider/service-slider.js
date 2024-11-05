document.addEventListener('turbo:load', loadServicesSliderData)

function loadServicesSliderData () {

}

listenHiddenBsModal('#createServiceSliderModal', function () {
    // filename = null;
    $('#inputImage').removeClass('image-input-changed')
    $('#createServiceImage').val('')
    $('.previewImage').
        css('background-image', 'url(' + $('#defaultServiceSliderDocumentImageUrl').val() + ')')
    $('#serviceSliderSaveBtn').attr('disabled', false)
})

listenHiddenBsModal('#editServiceSliderModal', function () {
    // filename = null;
    $('#editInputImage').removeClass('image-input-changed')
    $('#editServiceImage').val('')
    $('#serviceSliderEditBtnSave').attr('disabled', false)
})

listenClick('#serviceSliderSaveBtn', function () {
    // e.preventDefault()

    // let loadingButton = jQuery(this).find('#testimonialSaveBtn');
    // loadingButton.button('loading');
    $('#serviceSliderSaveBtn').attr('disabled', true)
    $.ajax({
        url: $('#superAdminServiceSliderStore').val(),
        type: 'POST',
        data: new FormData($('#serviceSliderForm')[0]),
        contentType: false,
        processData: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#createServiceSliderModal').modal('hide')
                $('#serviceSliderSaveBtn').attr('disabled', false)
                setTimeout(() => {
                    Livewire.dispatch('refresh')
                }, 2000)
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
            $('#serviceSliderSaveBtn').attr('disabled', false)
        },
        complete: function () {
            // loadingButton.button('reset');
        },
    })
})

listenClick('.service-slider-edit-btn', function (event) {
    let id = $(event.currentTarget).attr('data-id')
    renderServiceSliderData(id)
})

function renderServiceSliderData (id) {
    $.ajax({
        url: $('#superAdminServiceSliderIndex').val() + '/' + id + '/edit',
        type: 'GET',
        success: function (result) {
            $('#serviceId').val(result.data.id)
            $('#editDocumentImage').attr('src', result.data.image_url)
            $('#previewEditImage').
                css('background-image',
                    'url("' + result.data.image_url + '")')
            $('#editServiceSliderModal').modal('show')
        },
    })
}

listenChange('#serviceImage', function () {
    let extension = isValidFile($(this), '#serviceSliderValidationErrorsBox')
    if (!isEmpty(extension) && extension != false) {
        $('#serviceSliderValidationErrorsBox').html('').hide()
        displayServicePhoto(this, '#previewImage', extension)
    }
})

listenChange('#editServiceImage', function () {
    let extension = isValidFile($(this),
        '#editServiceSliderValidationErrorsBox')
    if (!isEmpty(extension) && extension != false) {
        $('#editServiceSliderValidationErrorsBox').html('').hide()
        displayServicePhoto(this, '#previewEditImage', extension)
    }
})

listenClick('#serviceSliderEditBtnSave', function () {
    // event.preventDefault()
    // let loadingButton = jQuery(this).find('#serviceSliderEditBtnSave');
    // loadingButton.button('loading');
    $('#serviceSliderEditBtnSave').attr('disabled', true)
    // var formData = new FormData($('#serviceSliderEditForm'))
    let id = $('#serviceId').val()
    $.ajax({
        url: $('#superAdminServiceSliderIndex').val() + '/' + id,
        type: 'POST',
        data: new FormData($('#serviceSliderEditForm')[0]),
        // dataType    : 'json',
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#editServiceSliderModal').modal('hide')
                $('#serviceSliderEditBtnSave').attr('disabled', false)
                Livewire.dispatch('refresh')
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
            $('#serviceSliderEditBtnSave').attr('disabled', false)
        },
    })
})

listenClick('.service-slider-delete-btn', function () {
    let serviceSliderId = $(this).attr('data-id')
    deleteItem($('#superAdminServiceSliderIndex').val() + '/' + serviceSliderId, null,
        $('#serviceSliderLang').val())
})

window.displayServicePhoto = function (input, selector) {
    let displayPreview = true
    if (input.files && input.files[0]) {
        let reader = new FileReader()
        reader.onload = function (e) {
            let image = new Image()
            image.src = e.target.result
            image.onload = function () {
                $(selector).attr('src', e.target.result)
                displayPreview = true
            }
        }
        if (displayPreview) {
            reader.readAsDataURL(input.files[0])
            $(selector).show()
        }
    }
}

