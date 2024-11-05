Livewire.hook("element.init", () => {
    $("#issuedItemHead").select2({
        width: "100%",
    });
});

listenClick('#issuedItemresetFilter', function () {
    $('#issuedItemHead').val(2).trigger('change')
})

listenClick('.deleteIssuedItemBtn', function (event) {
    let issuedItemId = $(event.currentTarget).attr('data-id')
    deleteItem($('#indexIssuedItemUrl').val() + '/' + issuedItemId,
        '#issuedItemsTable',
        $('#issuedItemLang').val())
})

listenClick('.changes-status-btn', function (event) {
    let issuedItemId = $(this).data('id')
    const issuedItemStatus = $(this).data('status')
    Lang.setLocale($('.userCurrentLanguage').val())
    if (!issuedItemStatus) {
        swal({
            title: Lang.get('js.change_status') + ' ' + '!',
            text: Lang.get('js.are_you_sure_want_to_return_this_item') + ' ' + '?',
            type: 'warning',
            icon: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: '#50cd89',
            showLoaderOnConfirm: true,
            buttons: {
                confirm: $('.yesVariable').val(),
                cancel: $('.noVariable').val(),
            },
        }).then(function (result) {
            if (result) {
                $.ajax({
                    url: $('#indexReturnIssuedItemUrl').val(),
                    type: 'get',
                    dataType: 'json',
                    data: { id: issuedItemId },
                    success: function (data) {
                        swal({
                            title: Lang.get('js.item_returned') + ' ' + '!',
                            icon: 'success',
                            confirmButtonColor: '#50cd89',
                            timer: 2000,
                            buttons: {
                                confirm: $('.okVariable').val()
                            }
                        })
                        Livewire.dispatch('refresh')
                    },
                })
            }
        })
    }
})
listenChange('#issuedItemHead', function () {
    Livewire.dispatch('changeFilter', {statusFilter: $(this).val()})
    hideDropdownManually($('#issuedItemFilter'), $('#issuedItemFilter'))
})
