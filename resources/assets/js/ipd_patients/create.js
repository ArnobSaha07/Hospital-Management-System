document.addEventListener('turbo:load', loadIpdPatientCreate)
// Livewire.hook("element.init", () => {
//     $("#ipd_patients_filter_status").select2({
//         width: "100%",
//     });
//     loadIpdPatientCreate();
// });
function loadIpdPatientCreate() {
    if (!$("#ipdAdmissionDate").length && !$("#editIpdAdmissionDate").length) {
        return;
    }

    var customDate = $("#customFieldDate").val();
    var customDateTime = $("#customFieldDateTime").val();

    $("#customFieldDate").flatpickr({
        defaultDate: customDate ? customDate : new Date(),
        dateFormat: "Y-m-d",
        locale: $(".userCurrentLanguage").val(),
    });

    $("#customFieldDateTime").flatpickr({
        enableTime: true,
        defaultDate: customDateTime ? customDateTime : new Date(),
        dateFormat: "Y-m-d H:i",
        locale: $(".userCurrentLanguage").val(),
    });

    $(
        "#ipdPatientId, #ipdDoctorId, #ipdBedTypeId,#editIpdPatientId, #editIpdDoctorId, #editIpdBedTypeId"
    ).select2({
        width: "100%",
    });

    $("#ipdCaseId, #editIpdCaseId ").select2({
        width: "100%",
        placeholder: Lang.get("js.choose_case"),
    });
    var ipdPlaceholder = $(".ipd-multi-select").data("placeholder");

    $(".ipd-multi-select").select2({
        placeholder: ipdPlaceholder,
    });

    $("#ipdBedId, #editIpdBedId").select2({
        width: "100%",
        placeholder: Lang.get("js.choose_bed"),
    });

    let admissionFlatPicker = $(
        "#ipdAdmissionDate, #editIpdAdmissionDate"
    ).flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        locale: $(".userCurrentLanguage").val(),
    });

    if ($(".isEdit").val()) {
        $(".ipdPatientId, .ipdBedTypeId").trigger("change");
        admissionFlatPicker.set("minDate", $(".ipdAdmissionDate").val());
    } else {
        admissionFlatPicker.setDate(new Date());
        admissionFlatPicker.set("minDate", new Date());
    }
}

listenKeyup(".ipdDepartmentFloatNumber", function () {
    if ($(this).val().indexOf(".") != -1) {
        if ($(this).val().split(".")[1].length > 2) {
            if (isNaN(parseFloat(this.value))) return;
            this.value = parseFloat(this.value).toFixed(2);
        }
    }
    return this;
});

listenSubmit(
    "#createIpdPatientForm, #editIpdPatientDepartmentForm",
    function () {
        $("#ipdSave, #btnIpdPatientEdit").attr("disabled", true);
    }
);

listenChange(".ipdPatientId", function () {
    if ($(this).val() !== "") {
        $.ajax({
            url: $(".patientCasesUrl").val(),
            type: "get",
            dataType: "json",
            data: { id: $(this).val() },
            success: function (data) {
                if (data.data.length !== 0) {
                    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").empty();
                    $(
                        "#ipdDepartmentCaseId,#editIpdDepartmentCaseId"
                    ).removeAttr("disabled");
                    $.each(data.data, function (i, v) {
                        $(
                            "#ipdDepartmentCaseId,#editIpdDepartmentCaseId"
                        ).append(
                            $("<option></option>").attr("value", i).text(v)
                        );
                    });
                    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId")
                        .val($("#editIpdPatientCaseId").val())
                        .trigger("change.select2");
                } else {
                    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").prop(
                        "disabled",
                        true
                    );
                }
            },
        });
    }
    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").empty();
    $("#ipdDepartmentCaseId,#editIpdDepartmentCaseId").prop("disabled", true);

    $("#ipdDepartmentCaseId, #editIpdDepartmentCaseId ").select2({
        width: "100%",
        placeholder: Lang.get("js.choose_case"),
    });
});

listenChange(".ipdBedTypeId", function () {
    let bedId = null;
    let bedTypeId = null;
    if (
        typeof $("#editIpdPatientBedId").val() != "undefined" &&
        typeof $("#editIpdPatientBedTypeId").val() != "undefined"
    ) {
        bedId = $("#editIpdPatientBedId").val();
        bedTypeId = $("#editIpdPatientBedTypeId").val();
    }

    if ($(this).val() !== "") {
        let bedType = $(this).val();
        $.ajax({
            url: $(".patientBedsUrl").val(),
            type: "get",
            dataType: "json",
            data: {
                id: $(this).val(),
                isEdit: $(".isEdit").val(),
                bedId: bedId,
                ipdPatientBedTypeId: bedTypeId,
            },
            success: function (data) {
                if (data.data !== null) {
                    if (data.data.length !== 0) {
                        $("#ipdBedId,#editIpdBedId").empty();
                        $("#ipdBedId,#editIpdBedId").removeAttr("disabled");
                        $.each(data.data, function (i, v) {
                            $("#ipdBedId,#editIpdBedId").append(
                                $("<option></option>").attr("value", i).text(v)
                            );
                        });
                        if (
                            typeof $("#editIpdPatientBedId").val() !=
                                "undefined" &&
                            typeof $("#editIpdPatientBedTypeId").val() !=
                                "undefined"
                        ) {
                            if (
                                bedType === $("#editIpdPatientBedTypeId").val()
                            ) {
                                $("#ipdBedId,#editIpdBedId")
                                    .val($("#editIpdPatientBedId").val())
                                    .trigger("change.select2");
                            }
                        }
                        $("#ipdBedId,#editIpdBedId").prop("required", true);
                    }
                } else {
                    $("#ipdBedId,#editIpdBedId").prop("disabled", true);
                }
            },
        });
    }
    $("#ipdBedId,#editIpdBedId").empty();
    $("#ipdBedId,#editIpdBedId").prop("disabled", true);

    $("#ipdBedId, #editIpdBedId").select2({
        width: "100%",
        placeholder: Lang.get("js.choose_bed"),
    });
});

function validateForm(formSelector, errorsBoxSelector) {
    var isValid = true;
    var form = $(formSelector);

    form.find(".dynamic-field").each(function () {
        var fieldValue = $(this).val();
        var fieldLabel = $(this)
            .closest(".form-group")
            .find("label")
            .text()
            .replace(":", "")
            .trim();

        if (
            $(this).is(':input[type="text"], :input[type="number"], textarea')
        ) {
            if (!fieldValue || fieldValue.trim() === "") {
                $(errorsBoxSelector)
                    .show()
                    .removeClass("d-none")
                    .html(fieldLabel + " field is required.")
                    .delay(5000)
                    .slideUp(300);
                isValid = false;
                return false;
            }
        } else if ($(this).is(':input[type="checkbox"]')) {
            if (!$(this).is(":checked")) {
                $(errorsBoxSelector)
                    .show()
                    .removeClass("d-none")
                    .html(fieldLabel + " field is required.")
                    .delay(5000)
                    .slideUp(300);
                isValid = false;
                return false;
            }
        } else if ($(this).is("select")) {
            if (
                !fieldValue &&
                $(this).val().length === 0 &&
                fieldValue.trim() === ""
            ) {
                $(errorsBoxSelector)
                    .show()
                    .removeClass("d-none")
                    .html("Please select " + fieldLabel)
                    .delay(5000)
                    .slideUp(300);
                isValid = false;
                return false;
            }
        }
    });

    event.preventDefault();

    if (isValid) {
        form.submit();
    }
}

listenClick("#ipdSave", function () {
    validateForm("#createIpdPatientForm", "#createIpdErrorsBox");
});

listenClick("#btnIpdPatientEdit", function () {
    validateForm("#editIpdPatientDepartmentForm", "#editIpdErrorsBox");
});
