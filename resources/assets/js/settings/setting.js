"use strict";

// document.addEventListener('turbo:load', loadGeneralSettingData)
Livewire.hook("element.init", () => {
    $("#sidebarFilterStatus").select2({
        width: "100%",
    });
    loadGeneralSettingData();
});

function loadGeneralSettingData() {
    $("#currencyType").select2({
        width: "100%",
    });

    listenChange("#appLogo , #favicon", function () {
        $("#generalValidationErrorsBox").addClass("d-none");
        if (isValidLogo($(this), "#generalValidationErrorsBox")) {
            displayLogo(this, "#logoPreviewImage");
        }
    });

    listenKeyup("#generalFacebookUrl", function () {
        this.value = this.value.toLowerCase();
    });
    listenKeyup("#generalTwitterUrl", function () {
        this.value = this.value.toLowerCase();
    });
    listenKeyup("#generalInstagramUrl", function () {
        this.value = this.value.toLowerCase();
    });
    listenKeyup("#generalLinkedInUrl", function () {
        this.value = this.value.toLowerCase();
    });

    listen("change", "#sidebarFilterStatus", function () {
        Livewire.dispatch("changeFilter", { statusFilter: $(this).val() });
        // $(tableName).DataTable().ajax.reload(null, true);
    });

    window.isValidLogo = function (inputSelector, validationMessageSelector) {
        let ext = $(inputSelector).val().split(".").pop().toLowerCase();
        if ($.inArray(ext, ["jpg", "png", "jpeg"]) == -1) {
            $(inputSelector).val("");
            $(validationMessageSelector).removeClass("d-none");
            $(validationMessageSelector)
                .html(Lang.get("js.image_must_be"))
                .show()
                .delay(5000)
                .slideUp(300);
            return false;
        }
        $(validationMessageSelector).hide();
        return true;
    };

    window.displayLogo = function (input, selector) {
        let displayPreview = true;
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let image = new Image();
                image.src = e.target.result;
                image.onload = function () {
                    if (image.height != 60 && image.width != 90) {
                        $(selector).val("");
                        $("#generalValidationErrorsBox").removeClass("d-none");
                        $("#generalValidationErrorsBox")
                            .html($(".settingImageValidation").val())
                            .show();
                        return false;
                    }
                    $(selector).attr("src", e.target.result);
                    displayPreview = true;
                };
            };
            if (displayPreview) {
                reader.readAsDataURL(input.files[0]);
                $(selector).show();
            }
        }
    };
    listenClick("#resetSidebarFilter", function () {
        $("#sidebarFilterStatus").val(0).trigger("change");
        hideDropdownManually($("#sidebarDropdownbtn"), $(".dropdown-menu"));
    });
}

listen("change", ".sidebar-status", function (event) {
    let moduleId = $(event.currentTarget).attr("data-id");
    updateSidebarSettingStatus(moduleId);
});

window.updateSidebarSettingStatus = function (id) {
    $.ajax({
        url: $("#moduleURL").val() + "/" + id + "/active-deactive",
        method: "post",
        cache: false,
        success: function (result) {
            if (result.success) {
                setTimeout(function () {
                    window.location.reload();
                }, 5000);
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

listenSubmit("#createHospitalSetting", function () {
    if ($("#error-msg").text() !== "") {
        $("#phoneNumber").focus();
        return false;
    }

    let facebookUrl = $("#generalFacebookUrl").val();
    let twitterUrl = $("#generalTwitterUrl").val();
    let instagramUrl = $("#generalInstagramUrl").val();
    let linkedInUrl = $("#generalLinkedInUrl").val();

    let facebookExp = new RegExp(
        /^(https?:\/\/)?((m{1}\.)?)?((w{2,3}\.)?)facebook.[a-z]{2,3}\/?.*/i
    );
    let twitterExp = new RegExp(
        /^(https?:\/\/)?((m{1}\.)?)?((w{2,3}\.)?)twitter\.[a-z]{2,3}\/?.*/i
    );
    let instagramUrlExp = new RegExp(
        /^(https?:\/\/)?((w{2,3}\.)?)instagram.[a-z]{2,3}\/?.*/i
    );
    let linkedInExp = new RegExp(
        /^(https?:\/\/)?((w{2,3}\.)?)linkedin\.[a-z]{2,3}\/?.*/i
    );

    let facebookCheck =
        facebookUrl == ""
            ? true
            : facebookUrl.match(facebookExp)
                ? true
                : false;
    if (!facebookCheck) {
        displayErrorMessage(Lang.get("js.please_enter_valid_facebook_url"));
        return false;
    }
    let twitterCheck =
        twitterUrl == "" ? true : twitterUrl.match(twitterExp) ? true : false;
    if (!twitterCheck) {
        displayErrorMessage(Lang.get("js.please_enter_valid_twitter_url"));
        return false;
    }
    let instagramCheck =
        instagramUrl == ""
            ? true
            : instagramUrl.match(instagramUrlExp)
                ? true
                : false;
    if (!instagramCheck) {
        displayErrorMessage(Lang.get("js.please_enter_valid_Instagram_url"));
        return false;
    }
    let linkedInCheck =
        linkedInUrl == ""
            ? true
            : linkedInUrl.match(linkedInExp)
                ? true
                : false;
    if (!linkedInCheck) {
        displayErrorMessage(Lang.get("js.please_enter_valid_Instagram_url"));
        return false;
    }
});
