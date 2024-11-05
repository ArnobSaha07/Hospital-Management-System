document.addEventListener('turbo:load', loadPatientData)

function loadPatientData() {
    if (!$("#createPatientForm").length && !$("#editPatientForm").length) {
        return;
    }

    var customDate = $("#customFieldDate").val();
    var customDateTime = $("#customFieldDateTime").val();

    var patientPlaceholder = $(".patient-multi-select").data("placeholder");

    $(".patient-multi-select").select2({
        placeholder: patientPlaceholder,
    });

    $(".patientBirthDate").flatpickr({
        maxDate: new Date(),
        locale: $(".userCurrentLanguage").val(),
    });

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
}
listenKeyup(".patientFacebookUrl", function () {
    this.value = this.value.toLowerCase();
});
listenKeyup(".patientTwitterUrl", function () {
    this.value = this.value.toLowerCase();
});
listenKeyup(".patientInstagramUrl", function () {
    this.value = this.value.toLowerCase();
});
listenKeyup(".patientLinkedInUrl", function () {
    this.value = this.value.toLowerCase();
});

function validateForm(formSelector) {
    var isValid = true;
    var form = $(formSelector);

    if ($(".error-msg").text() !== "") {
        $(".phoneNumber").focus();
        return false;
    }

    let facebookUrl = $(".patientFacebookUrl").val();
    let twitterUrl = $(".patientTwitterUrl").val();
    let instagramUrl = $(".patientInstagramUrl").val();
    let linkedInUrl = $(".patientLinkedInUrl").val();

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
                displayErrorMessage(fieldLabel + " field is required.");
                isValid = false;
                return false;
            }
        } else if ($(this).is(':input[type="checkbox"]')) {
            if (!$(this).is(":checked")) {
                displayErrorMessage(fieldLabel + " field is required.");
                isValid = false;
                return false;
            }
        } else if ($(this).is("select")) {
            if (
                !fieldValue &&
                $(this).val().length === 0 &&
                fieldValue.trim() === ""
            ) {
                displayErrorMessage("Please select " + fieldLabel);
                isValid = false;
                return false;
            }
        }
    });

    Lang.setLocale($(".userCurrentLanguage").val());

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
        displayErrorMessage(Lang.get("js.please_enter_valid_linkedin_url"));
        return false;
    }

    event.preventDefault();

    if (isValid) {
        form.submit();
    }
}

listenClick("#btnSave", function () {
    validateForm("#createPatientForm");
});

listenClick("#editPatientSave", function () {
    validateForm("#editPatientForm");
});

$("#createPatientForm, #editPatientForm")
    .find("input:text:visible:first")
    .focus();

listenClick(".remove-patient-image", function () {
    defaultImagePreview(".previewImage", 1);
});
