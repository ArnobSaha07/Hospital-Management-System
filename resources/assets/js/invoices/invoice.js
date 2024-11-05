// document.addEventListener('turbo:load', loadAdminInvoiceData)
Livewire.hook("element.init", () => {
    $("#invoice_status_filter").select2({
        width: "100%",
    });
    loadAdminInvoiceData();
});
function loadAdminInvoiceData() {
    listen("click", "#resetEmployeeInvoiceFilter", function () {
        $("#invoice_status_filter").val(2).trigger("change");
        hideDropdownManually($("#invoiceFilterBtn"), $(".dropdown-menu"));
    });
}

listen("click", ".deleteInvoicesBtn", function (event) {
    let id = $(event.currentTarget).attr("data-id");
    deleteItem(
        $("#indexInvoiceUrl").val() + "/" + id,
        "",
        $("#invoiceLang").val()
    );
});

listenChange("#invoice_status_filter", function () {
    Livewire.dispatch("changeFilter", { statusFilter: $(this).val() });
});

listen("click", ".sendInvoiceBtn", function (event) {
    event.preventDefault();
    let loadingButton = $(this);
    loadingButton.text(Lang.get("js.sending"));
    loadingButton.attr("disabled", true);
    $.ajax({
        url: $("#sendInvoiceRouteId").val(),
        type: "GET",
        success: function (result) {
            if (result.success) {
                loadingButton.text(Lang.get("js.send_mail"));
                displaySuccessMessage(result.message);
                setTimeout(function () {
                    loadingButton.attr("disabled", false);
                }, 6000);
            }
        },
        error: function (result) {
            loadingButton.attr("disabled", false);
            manageAjaxErrors(result);
        },
        complete: function () {
            loadingButton.button("reset");
        },
    });
});
