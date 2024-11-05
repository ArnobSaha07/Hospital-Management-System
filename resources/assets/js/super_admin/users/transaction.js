// document.addEventListener('turbo:load', loadUserTransactionDate)
Livewire.hook("element.init", () => {
    $("#paymentType").select2({
        width: "100%",
    });
    loadUserTransactionDate();
});
function loadUserTransactionDate() {}

listenClick("#hospitalTransactionsResetFilter", function () {
    $("#paymentType").val("").trigger("change");
    hideDropdownManually(
        $("#hospitalTransactionsFilterButton"),
        $(".dropdown-menu")
    );
});

listenChange("#paymentType", function () {
    Livewire.dispatch("changeFilter", { statusFilter: $(this).val() });
});
