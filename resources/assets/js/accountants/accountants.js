Livewire.hook("element.init", () => {
    $("#accountant_filter_status").select2({
        width: "100%",
    });
});
listenClick(".accountant-delete-btn", function (event) {
    let accountantId = $(event.currentTarget).attr("data-id");
    deleteItem(
        $("#accountantIndexURL").val() + "/" + accountantId,
        "#accountantsTbl",
        $("#Accountant").val()
    );
});

listenChange(".accountant-status", function (event) {
    let accountantId = $(event.currentTarget).attr("data-id");
    updateAccountantStatus(accountantId);
});

window.updateAccountantStatus = function (id) {
    $.ajax({
        url: $("#accountantIndexURL").val() + "/" + +id + "/active-deactive",
        method: "post",
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                Livewire.dispatch("refresh");
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
};

listen("change", "#accountant_filter_status", function () {
    Livewire.dispatch("changeFilter", { statusFilter: $(this).val() });
});

listenClick("#accountantResetFilter", function () {
    $("#accountant_filter_status").val(0).trigger("change");
    hideDropdownManually($("#accountantFilterBtn"), $(".dropdown-menu"));
});
