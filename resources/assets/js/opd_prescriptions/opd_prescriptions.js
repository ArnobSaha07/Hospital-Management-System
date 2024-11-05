document.addEventListener("turbo:load", loadOpdPrescriptionData);

function loadOpdPrescriptionData() {
    if (
        !$("#editOpdPrescriptionModal").length &&
        !$("#addOpdPrescriptionModal").length
    ) {
        return;
    }
    $(".opdCategoryId,.medicineId,.opdDoseDuration,.opdDoseInterval,.opdMealList").select2({
        width: "100%",
    });

    listen("click", ".deleteOpdPrescriptionBtn", function (event) {

        let id = $(event.currentTarget).attr("data-id");
        deleteItem(
            route("opd.prescription.destroy",id),
            "",
            $("#opdPrescriptionLang").val()
        );
    });


    listenClick(
        ".deleteOpdPrescription, .deleteOpdPrescriptionOnEdit",
        function () {
            $(this).parents("tr").remove();
            resetOpdPrescriptionItemIndex(parseInt($(this).data("edit")));
        }
    );

    listenChange(".opdCategoryId", function (e, rData) {
        const medicineId = $(document).find(
            "[data-medicine-id='" + $(this).data("id") + "']"
        );
        let uniqueMedicineId = $(this).attr("data-id");
        if ($(this).val() !== "") {
            $.ajax({
                url: route("opd.medicine.list"),
                type: "get",
                dataType: "json",
                data: { id: $(this).val() },
                success: function (data) {
                    if (data.data.length !== 0) {
                        medicineId.empty();
                        medicineId.removeAttr("disabled");
                        medicineId.append(
                            $("<option></option>").text(
                                Lang.get(
                                    "js.select_medicine"
                                )
                            )
                        );
                        $.each(data.data, function (i, v) {
                            medicineId.append(
                                $("<option></option>").attr("value", i).text(v)
                            );
                        });
                        $(".available-qty-div" + uniqueMedicineId)
                            .find("small")
                            .text("")
                            .end()
                            .css("margin-top", "0px");
                        if ($(".modal").hasClass("show")) {
                            medicineSelect2($(".modal.fade.show").attr("id"));
                        }
                        if (typeof rData != "undefined") {
                            medicineId
                                .val(rData.medicineId)
                                .trigger("change.select2");
                        }
                    } else {
                        medicineId.append(
                            $("<option></option>").text(
                                Lang.get(
                                    "js.select_medicine"
                                )
                            )
                        );
                        $(".available-qty-div" + uniqueMedicineId)
                            .find("small")
                            .text("")
                            .end()
                            .css("margin-top", "0px");
                    }
                },
            });
        }
        medicineId.empty();
        medicineId.prop("disabled", true);
    });

    listenClick(".editOpdPrescriptionBtn", function (event) {
        if ($(".ajaxCallIsRunning").val()) {
            return;
        }
        ajaxCallInProgress();
        let opdPrescriptionId = event.currentTarget.dataset.id;
        renderOpdPrescriptionData(opdPrescriptionId);
    });

    function renderOpdPrescriptionData(id) {
        $.ajax({
            url: route("opd.prescription.edit",id),
            type: "GET",
            success: function (result) {
                if (result.success) {
                    let opdPrescriptionData = result.data.opdPrescription;
                    let opdPrescriptionItemsData =
                        result.data.opdPrescriptionItems;
                    $("#opdEditPrescriptionId").val(opdPrescriptionData.id);
                    $("#editOpdHeaderNote").val(opdPrescriptionData.header_note);
                    $("#editOpdFooterNote").val(opdPrescriptionData.footer_note);

                    $.each(opdPrescriptionItemsData, function (i, v) {
                        $("#addOpdPrescriptionItemOnEdit").trigger("click");
                        let rowId = $("#showOpdUniqueId").val() - 1;
                        var element = $(document).find(
                            "[data-avlMedicine-id='" + rowId + "']"
                        );
                        var availableQuantity = v.medicine.available_quantity;
                        var message =
                            Lang.get(
                                "js.available_quantity"
                            ) +
                            ": " +
                            availableQuantity;

                        element
                            .text(message)
                            .addClass(
                                availableQuantity <= 10
                                    ? "text-danger"
                                    : "text-success"
                            );

                        $(document)
                            .find("[data-id='" + rowId + "']")
                            .val(v.category_id)
                            .trigger("change", [{ medicineId: v.medicine_id }]);

                        $(document)
                            .find("[data-dose-duration-id='" + rowId + "']")
                            .val(v.day)
                            .trigger("change", [{ medicineId: v.medicine_id }]);

                        $(document)
                            .find("[data-dose-interval-id='" + rowId + "']")
                            .val(v.dose_interval)
                            .trigger("change", [{ medicineId: v.medicine_id }]);

                        $(document)
                            .find("[data-meal-id='" + rowId + "']")
                            .val(v.time)
                            .trigger("change", [{ medicineId: v.medicine_id }]);
                        $(document)
                            .find("[data-dosage-id='" + rowId + "']")
                            .val(v.dosage);
                        $(document)
                            .find("[data-instruction-id='" + rowId + "']")
                            .val(v.instruction);
                    });

                    let index = 1;
                    $(".edit-opd-prescription-item-container>tr").each(
                        function () {
                            $(this)
                                .find(".opd-prescription-item-numberr")
                                .text(index);
                            index++;
                        }
                    );

                    $("#editOpdPrescriptionModal").modal("show");
                    ajaxCallCompleted();
                }
            },
            error: function (result) {
                manageAjaxErrors(result);
            },
        });
    }

    listenSubmit("#editOpdPrescriptionForm", function (event) {
        event.preventDefault();
        if (checkOpdMedicine() !== true) {
            return false;
        }
        let loadingButton = jQuery(this).find("#btnEditOpdPrescriptionSave");
        loadingButton.button("loading");
        let id = $("#opdEditPrescriptionId").val();
        let url = $("#showOpdPrescriptionUrl").val() + "/" + id;
        // console.log(url);
        let data = {
            formSelector: $(this),
            url: url,
            type: "POST",
            // 'tableSelector': tableName,
        };
        editRecord(data, loadingButton, "#editOpdPrescriptionModal");
    });

    // listenClick(".printOpdPrescription", function () {
    //     let divToPrint = document.getElementById("DivIdToPrint");
    //     let newWin = window.open("", "Print-Window");
    //     newWin.document.open();
    //     newWin.document.write(
    //         '<html><link href="' +
    //             $("#showOpdBootstrapUrl").val() +
    //             '" rel="stylesheet" type="text/css"/>' +
    //             '<body onload="window.print()">' +
    //             divToPrint.innerHTML +
    //             "</body></html>"
    //     );
    //     newWin.document.close();
    //     setTimeout(function () {
    //         newWin.close();
    //     }, 10);
    // });

    listenHiddenBsModal("#addOpdPrescriptionModal", function () {
        $("#avlQtyDiv").find("small").text("").end().css("margin-top", "0px");
        resetModalForm("#addOpdPrescriptionForm", "#opdPrescriptionErrorsBox");
        $("#opdPrescriptionTbl").find("tr:gt(1)").remove();
        $(".opdCategoryId").val("");
        $(".opdCategoryId").trigger("change");
    });

    listenShownBsModal("#addOpdPrescriptionModal", function () {
        medicineSelect2(".medicineId");
        dropdownToSelect2("#opdPrescriptionItemTemplate");
    });

    listenHiddenBsModal("#editOpdPrescriptionModal", function () {
        $("#avlQtyDiv").find("small").text("").end().css("margin-top", "0px");
        $("#editOpdPrescriptionTbl").find("tr:gt(0)").remove();
        resetModalForm(
            "#editOpdPrescriptionForm",
            "#editOpdPrescriptionErrorsBox"
        );
    });

    listenClick(".viewOpdPrescription", function (event) {
        let opdPrescriptionShowId = event.currentTarget.dataset.id;
        $.ajax({
            url:$("#showOpdPrescriptionUrl").val() +"/" + opdPrescriptionShowId,
            type: "get",
            beforeSend: function () {
                screenLock();
            },
            success: function (result) {
                $("#opdPrescriptionViewData").html(result);
                $("#showOpdPrescriptionModal").modal("show");
                ajaxCallCompleted();
            },
            complete: function () {
                screenUnLock();
            },
        });
    });
}
const dropdownToSelect2 = (selector) => {
    if (selector === "#opdPrescriptionItemTemplate") {
        $(".opdCategoryId").select2({
            placeholder: Lang.get("js.select_category"),
            width: "100%",
            dropdownParent: $("#addOpdPrescriptionModal"),
        });
        $(".opdDoseDuration,.opdDoseInterval,.opdMealList").select2({
            width: "100%",
        });
        $(".medicineId").select2({
            placeholder: Lang.get("js.select_medicine"),
            width: "100%",
            dropdownParent: $("#addOpdPrescriptionModal"),
        });
    } else {
        $(".opdCategoryId").select2({
            placeholder: Lang.get("js.select_category"),
            width: "100%",
            dropdownParent: $("#editOpdPrescriptionModal"),
        });
        $(".opdDoseDuration,.opdDoseInterval,.opdMealList").select2({
            width: "100%",
        });
        $(".medicineId").select2({
            placeholder: Lang.get("js.select_medicine"),
            width: "100%",
            dropdownParent: $("#editOpdPrescriptionModal"),
        });
    }
};

dropdownToSelect2("#opdPrescriptionItemTemplate");

const medicineSelect2 = (selector) => {
    if (selector === "addOpdPrescriptionModal") {
        $(".medicineId").select2({
            placeholder: Lang.get('js.select_medicine'),
            width: "100%",
            dropdownParent: $("#addOpdPrescriptionModal"),
        });
    } else {
        $(".medicineId").select2({
            placeholder: Lang.get('js.select_medicine'),
            width: "100%",
            dropdownParent: $("#editOpdPrescriptionModal"),
        });
    }
};

listenClick(
    "#addOpdPrescriptionItem, #addOpdPrescriptionItemOnEdit",
    function () {
        const itemSelector = parseInt($(this).data("edit"))
            ? "#editopdPrescriptionItemTemplate"
            : "#opdPrescriptionItemTemplate";
        const tbodyItemSelector = parseInt($(this).data("edit"))
            ? ".edit-opd-prescription-item-container"
            : ".opd-prescription-item-container";
        let uniqueId = $("#showOpdUniqueId").val();
        let data = {
            medicineCategories: JSON.parse($("#showOpdMedicineCategories").val()),
            opdDoseDuration: JSON.parse($(".opdPrescriptionDurations").val()),
            opdDoseInterval: JSON.parse($(".opdPrescriptionIntervals").val()),
            opdMealList: JSON.parse($(".opdPrescriptionMeals").val()),
            uniqueId: uniqueId,
        };
        let opdPrescriptionItemHtml = prepareTemplateRender(
            itemSelector,
            data
        );
        $(tbodyItemSelector).append(opdPrescriptionItemHtml);
        dropdownToSelect2(itemSelector);
        uniqueId++;
        $("#showOpdUniqueId").val(uniqueId);

        resetOpdPrescriptionItemIndex(parseInt($(this).data("edit")));

    }
);

const resetOpdPrescriptionItemIndex = (itemMode) => {
    const itemSelector = itemMode
        ? "#editopdPrescriptionItemTemplate"
        : "#opdPrescriptionItemTemplate";
    const tbodyItemSelector = itemMode
        ? ".edit-opd-prescription-item-container"
        : ".opd-prescription-item-container";
    const itemNo = itemMode
        ? ".edit-opd-prescription-item-number"
        : ".opd-prescription-item-number";

    let index = 1;
    $(tbodyItemSelector + ">tr").each(function () {
        $(this).find(itemNo).text(index);
        index++;
    });
    let uniqueId = $("#showOpdUniqueId").val();
    if (index - 1 == 0) {
        let data = {
            medicineCategories: JSON.parse($("#showOpdMedicineCategories").val()),
            opdDoseDuration: JSON.parse($(".opdPrescriptionDurations").val()),
            opdDoseInterval: JSON.parse($(".opdPrescriptionIntervals").val()),
            opdMealList: JSON.parse($(".opdPrescriptionMeals").val()),
            uniqueId: uniqueId,
        };
        let opdPrescriptionItemHtml = prepareTemplateRender(
            itemSelector,
            data
        );
        $(tbodyItemSelector).append(opdPrescriptionItemHtml);
        dropdownToSelect2(itemSelector);
        uniqueId++;
    }
};

listenSubmit("#addOpdPrescriptionForm", function (event) {
    event.preventDefault();
    if (checkOpdMedicine() !== true) {
        return false;
    }
    let loadingButton = jQuery(this).find("#btnOpdPrescriptionSave");
    loadingButton.button("loading");
    let data = {
        formSelector: $(this),
        url: $("#showOpdPrescriptionCreateUrl").val(),
        type: "POST",
    };
    newRecord(data, loadingButton, "#addOpdPrescriptionModal");
});

function checkOpdMedicine() {
    let result = true;
    $(".medicineId").each(function xyz() {
        if ($(this).val() == "Select Medicine") {
            displayErrorMessage(
                Lang.get("js.medicine_required")
            );
            result = false;
            return false;
        }
    });
    return result;
}

listenChange(".opd-prescription-medicine", function () {
    let medicineId = $(this).val();
    let uniqueId = $(this).attr("data-medicine-id");
    if (medicineId == Lang.get("js.select_medicine")) {
        $(".available-qty-div" + uniqueId)
            .find("small")
            .text("")
            .end()
            .css("margin-top", "0px");
    }
    $.ajax({
        url: route("get-medicine-quantity", medicineId),
        method: "get",
        cache: false,
        success: function (result) {
            if (result.data == null) {
                $(".available-qty-div" + uniqueId)
                    .find("small")
                    .text("")
                    .end()
                    .css("margin-top", "0px");
                return false;
            }
            if (result.success) {
                if (result.data.available_quantity <= 10) {

                    $(".opd-available-quantity" + uniqueId)
                        .text(
                            Lang.get("js.available_quantity") +
                                " : " +
                                result.data.available_quantity
                        )
                        .addClass("text-danger")
                        .removeClass("text-success");
                    $(".available-qty-div" + uniqueId).css({
                        "margin-top": "22px",
                    });
                } else {
                    $(".opd-available-quantity" + uniqueId)
                        .text(
                            Lang.get("js.available_quantity") +
                                " : " +
                                result.data.available_quantity
                        )
                        .addClass("text-success")
                        .removeClass("text-danger");
                    $(".available-qty-div" + uniqueId).css({
                        "margin-top": "22px",
                    });
                }
            }
        },
    });
});
