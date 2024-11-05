"use strict";
// document.addEventListener('turbo:load', loadServicesCreateEdit)
Livewire.hook("element.init", () => {
    $("#servicesFilterStatus").select2({
        width: "100%",
    });
    loadServicesCreateEdit();
});
function loadServicesCreateEdit() {
    listenChange("#servicesFilterStatus", function () {
        Livewire.dispatch("changeFilter", { statusFilter: $(this).val() });
    });
    listenClick("#servicesResetFilter", function () {
        $("#servicesFilterStatus").val(0).trigger("change");
        hideDropdownManually($("#servicesFilterBtn"), $(".dropdown-menu"));
    });
}

listenClick(".services-delete-btn", function (event) {
    let serviceId = $(event.currentTarget).attr("data-id");
    deleteItem(
        $("#showServiceReportUrl").val() + "/" + serviceId,
        "#servicesReportTable",
        $("#serviceLang").val()
    );
});

listenChange(".service-status", function (event) {
    let serviceId = $(event.currentTarget).attr("data-id");
    updateServiceStatus(serviceId);
});

window.updateServiceStatus = function (id) {
    $.ajax({
        url: $("#showServiceReportUrl").val() + "/" + id + "/active-deactive",
        method: "post",
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                Livewire.dispatch("refresh");
                // tbl.ajax.reload(null, false);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
};
