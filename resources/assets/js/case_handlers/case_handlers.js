Livewire.hook("element.init", () => {
    $("#caseHandlerHead").select2({
        width: "100%",
    });
});

listenClick('.case-handler-delete-btn', function (event) {
    let caseHandlerId = $(event.currentTarget).data('id')
    deleteItem($('#indexCaseHandlerUrl').val() + '/' + caseHandlerId,
        '#caseHandlersTbl',
        $('#caseHandlerLang').val())
})

listenChange('#caseHandlerHead', function () {
    Livewire.dispatch('changeFilter', {statusFilter: $(this).val()})
    hideDropdownManually($('#caseHandlerFilterBtn'), $('.dropdown-menu'))
})

listenChange('.case-handler-status', function (event) {
    let caseHandlerId = $(event.currentTarget).data('id')
    updateCaseHandlerStatus(caseHandlerId)
})

listenClick('#caseHandlerResetFilter', function () {
    $('#caseHandlerHead').val(2).trigger('change')
    hideDropdownManually($('#caseHandlerFilterBtn'), $('.dropdown-menu'))
})

window.updateCaseHandlerStatus = function (id) {
    $.ajax({
        url: $('#indexCaseHandlerUrl').val() + '/' + id + '/active-deactive',
        method: 'post',
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                Livewire.dispatch('refresh')
                // tbl.ajax.reload(null, false)
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
    })
}

