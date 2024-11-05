document.addEventListener("turbo:load", smartPatientCardDataLoad);

function smartPatientCardDataLoad() {
    $("#cardTemplateID,.patient-id").select2({
        width: "100%",
        dropdownParent: $("#addPatientCardModal"),
    });

    if (!$(".header-color-wrapper").length) {
        return false;
    }

    const primaryColorPickr = Pickr.create({
        el: ".header-color-wrapper",
        theme: "nano", // or 'monolith', or 'nano'
        closeWithKey: "Enter",
        autoReposition: true,
        defaultRepresentation: "HEX",
        position: "bottom-start",
        swatches: [
            "rgba(244, 67, 54, 1)",
            "rgba(233, 30, 99, 1)",
            "rgba(156, 39, 176, 1)",
            "rgba(103, 58, 183, 1)",
            "rgba(63, 81, 181, 1)",
            "rgba(33, 150, 243, 1)",
            "rgba(3, 169, 244, 1)",
            "rgba(0, 188, 212, 1)",
            "rgba(0, 150, 136, 1)",
            "rgba(76, 175, 80, 1)",
            "rgba(139, 195, 74, 1)",
            "rgba(205, 220, 57, 1)",
            "rgba(255, 235, 59, 1)",
            "rgba(255, 193, 7, 1)",
        ],

        components: {
            // Main components
            preview: true,
            hue: true,

            // Input / output Options
            interaction: {
                input: true,
                clear: false,
                save: false,
            },
        },
    });
    $("#headerColorId").val(primaryColorPickr.getColor().toHEXA().toString());

    primaryColorPickr.on("change", function () {
        const headerColor = primaryColorPickr.getColor().toHEXA().toString();
        $(".custom-card-header").css("background-color", headerColor);
        if (!$("#editHeaderColorId").length) {
            primaryColorPickr.setColor(headerColor);
            $("#headerColorId").val(headerColor);
        } else {
            primaryColorPickr.setColor(headerColor);
            $("#editHeaderColorId").val(headerColor);
        }
    });

    primaryColorPickr.on("init", function () {
        primaryColorPickr.setColor($("#editHeaderColorId").val());
    });
    // }

    // Patient email
    $("#showEmail").on("change", function () {
        if ($(this).prop("checked") == false) {
            $("#patientEmail").addClass("d-none");
        } else {
            $("#patientEmail").removeClass("d-none");
        }
    });
    // Patient phone
    $("#showPhone").on("change", function () {
        if ($(this).prop("checked") == false) {
            $("#patientNumber").addClass("d-none");
        } else {
            $("#patientNumber").removeClass("d-none");
        }
    });
    // Patient dob
    $("#showDob").on("change", function () {
        if ($(this).prop("checked") == false) {
            $("#patientDob").addClass("d-none");
        } else {
            $("#patientDob").removeClass("d-none");
        }
    });
    // Patient blood group
    $("#showBloodGroup").on("change", function () {
        if ($(this).prop("checked") == false) {
            $("#patientBloodGroup").addClass("d-none");
        } else {
            $("#patientBloodGroup").removeClass("d-none");
        }
    });
    // Patient address
    $("#showAddress").on("change", function () {
        if ($(this).prop("checked") == false) {
            $("#patientAddress").addClass("d-none");
        } else {
            $("#patientAddress").removeClass("d-none");
        }
    });
    // Patient unique ID
    $("#showUniqueID").on("change", function () {
        if ($(this).prop("checked") == false) {
            $("#patientUniqueID").addClass("d-none");
        } else {
            $("#patientUniqueID").removeClass("d-none");
        }
    });
}
listenChange(
    "#show_email, #show_phone, #show_address, #show_blood_group, #show_dob, #show_patient_unique_id",
    function () {
        let status = $(this).prop("checked") ? 1 : 0;
        let id = $(this).attr("data-id");
        let name = $(this).attr("name");
        $.ajax({
            url: route("patient-smart-card-templates.status", id),
            type: "post",
            data: { status: status, name: name },
            success: function (data) {
                if (data.success) {
                    displaySuccessMessage(data.message);
                    Livewire.dispatch("refresh");
                }
            },
        });
    }
);

listenChange("#color", function () {
    let id = $(this).attr("data-id");
    let color = $(this).val();
    $.ajax({
        type: "post",
        url: route("patient-smart-card-templates.status", id),
        data: { color: color },
        success: function (data) {
            if (data.success) {
                displaySuccessMessage(data.message);
                Livewire.dispatch("refresh");
            }
        },
    });
});

listenClick(".delete-smart-card-template", function (event) {
    let smartCardId = $(event.currentTarget).attr("data-id");
    deleteItem(
        route("patient-smart-card-templates.destroy", smartCardId),
        "",
        Lang.get("js.card_template")
    );
});

listenClick(".delete-smart-patient-card-btn", function (event) {
    let patientCardId = $(event.currentTarget).attr("data-id");
    deleteItem(
        route("smart-patient-cards.destroy", patientCardId),
        "",
        Lang.get("js.smart_patient_card")
    );
});

listenClick("#onePatient", function (event) {
    $(".customisePatient").removeClass("d-none");
    $.ajax({
        url: route("get.patients"),
        type: "GET",
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                $("#cardTemplatePatientID").find("option").remove();
                $("#cardTemplatePatientID").append(
                    $("<option></option>")
                        .attr("placeholder", "")
                        .text(Lang.get("js.select_patient"))
                );
                $.each(result.data, function (i, v) {
                    $("#cardTemplatePatientID").append(
                        $("<option></option>").attr("value", i).text(v)
                    );
                });
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});

listenClick("#allPatient,#remainingPatient", function () {
    $(".customisePatient").addClass("d-none");
});

listenHiddenBsModal("#addPatientCardModal", function () {
    resetModalForm("#addPatientCardForm", "#patientCardErrorsBox");
    $(".template-name").trigger("change");
    $(".patient-id").trigger("change");
    $(".customisePatient").addClass("d-none");
    $("#patientCardSave").attr("disabled", false);
});

listenSubmit("#addPatientCardForm", function (event) {
    event.preventDefault();
    if ($.trim($(".template-name").val()) == "") {
        displayErrorMessage(Lang.get("js.template_required"));
        return false;
    }
    if (
        $(".one-patient").is(":checked") == true &&
        $.trim($(".patient-id").val()) == ""
    ) {
        displayErrorMessage(Lang.get("js.patient_required"));
        return false;
    }
    var loadingButton = jQuery(this).find("#patientCardSave");
    loadingButton.button("loading");
    $("#patientCardSave").attr("disabled", true);
    $.ajax({
        url: route("smart-patient-cards.store"),
        type: "POST",
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $("#addPatientCardModal").modal("hide");
                Livewire.dispatch("refresh");
                $("#patientCardSave").attr("disabled", false);
            }
        },
        error: function (result) {
            printErrorMessage("#patientCardErrorsBox", result);
            $("#patientCardSave").attr("disabled", false);
        },
        complete: function () {
            loadingButton.button("reset");
        },
    });
});

listenClick(".show-patient-smart-card", function (event) {
    event.preventDefault();
    let patientId = $(event.currentTarget).attr("data-id");
    $('#patientCardAddress,#patientCardBloodGroup,#patientCardDob,#uniqueCardID,#patientCardEmail,patientCardMob').text("");
    $('.patientAddress,.patientBloodGroup,.patientDob,.patient-code,.patientEmail,.patientNumber').addClass('d-none');
    $.ajax({
        url: route("smart-patient-cards.show", patientId),
        type: "GET",
        success: function (result) {
            if (result.success) {
                let patientAddress = result.data.address;
                $("#patientCardBloodGroup").text()
                $(".profile-img").attr("src", result.data.profile);
                $(".patient-card-header").css(
                    "background-color",
                    result.data.smart_card_template.header_color
                );
                $(".download-icon").css(
                    "color",
                    result.data.smart_card_template.header_color
                );
                $("#patientCardName").text(result.data.patient_user.full_name);
                if (
                    result.data.smart_card_template.show_address == true &&
                    !isEmpty(patientAddress)
                ) {
                    $(".patientAddress").removeClass("d-none");
                    $("#patientCardAddress").text(patientAddress.address1);
                }
                if (
                    result.data.smart_card_template.show_blood_group == true &&
                    !isEmpty(result.data.patient_user.blood_group)
                ) {
                    $(".patientBloodGroup").removeClass("d-none");
                    $("#patientCardBloodGroup").text(
                        result.data.patient_user.blood_group
                    );
                }
                if (
                    result.data.smart_card_template.show_dob == true &&
                    !isEmpty(result.data.patient_user.dob)
                ) {
                    $(".patientDob").removeClass("d-none");
                    $("#patientCardDob").text(result.data.patient_user.dob);
                }
                if (
                    result.data.smart_card_template.show_patient_unique_id ==
                    true
                ) {
                    $(".patient-code").removeClass("d-none");
                    $("#uniqueCardID").text(result.data.patient_unique_id);
                }
                if (result.data.smart_card_template.show_email == true) {
                    $(".patientEmail").removeClass("d-none");
                    $("#patientCardEmail").text(result.data.patient_user.email);
                }
                if (
                    result.data.smart_card_template.show_phone == true &&
                    !isEmpty(result.data.patient_user.phone)
                ) {
                    $(".patientNumber").removeClass("d-none");
                    $("#patientCardMob").text(result.data.patient_user.phone);
                }

                $("#showSmartCardModal").modal("show");
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });

    $.ajax({
        url: route("smart.card.qr.code", patientId),
        type: "GET",
        success: function (data) {
            $(".qr-code").html(data);
        },
        error: function (data) {
            displayErrorMessage(
                Lang.get("js.qr_code_not_found")
            );
        },
    });
});
