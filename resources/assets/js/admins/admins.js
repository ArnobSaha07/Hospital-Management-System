document.addEventListener('turbo:load', loadAdminData)

function loadAdminData() {
    
}
listenSubmit('#createAdminForm, #editAdminForm', function () {
    if ($('.error-msg').text() !== '') {
        $('.phoneNumber').focus();
        return false;
    }
});

listenClick('.admin-delete-btn', function (event) {
    let adminId = $(event.currentTarget).attr('data-id')
    deleteItem($('#adminUrl').val() + '/' + adminId, '', $('#Admin').val())
})

listenChange('.admin-status', function (event) {
    let adminId = $(event.currentTarget).attr('data-id')
    updateAdminStatus(adminId)
})

window.updateAdminStatus = function (id) {
    $.ajax({
        url: $('#adminUrl').val() + '/' +id + '/active-deactive',
        method: 'post',
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                Livewire.dispatch('refresh')
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
    })
}
