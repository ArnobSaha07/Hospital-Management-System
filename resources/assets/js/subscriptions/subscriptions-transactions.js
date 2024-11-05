
// document.addEventListener('turbo:load', loadSubTransactionsData)
Livewire.hook("element.init", () => {
    $("#paymentTypeArr").select2({
        width: "100%",
    });
    loadSubTransactionsData();
});
function loadSubTransactionsData() {

    listenChange('#paymentTypeArr', function () {
        Livewire.dispatch('changeFilter', {statusFilter: $(this).val()})
        // $(tableName).DataTable().ajax.reload(null, true);
    });
}

listenChange('.payment-approve', function () {
    let id = $(this).attr('data-id');
    let status = $(this).val();

    $.ajax({
        url: route('change-payment-status', id),
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

listenClick('#transactionSideResetFilter', function () {
    $('#paymentTypeArr').val(9).trigger('change')
    hideDropdownManually($('#subscriptionTransaction'), $('.dropdown-menu'))
})
