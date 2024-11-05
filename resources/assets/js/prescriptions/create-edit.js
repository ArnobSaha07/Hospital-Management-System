document.addEventListener("turbo:load", loadPrescriptionCreate);
// Livewire.hook("element.init", () => {
//     $("#prescriptionHead").select2({
//         width: "100%",
//     });
//     loadPrescriptionCreate();
// });
let uniquePrescriptionId = $(".prescriptionUniqueId").val();

function loadPrescriptionCreate() {
    if (
        !$("#prescriptionPatientId").length &&
        !$("#editPrescriptionPatientId").length
    ) {
        return;
    }
    const prescriptionAddedAtElement = $("#prescriptionAddedAt");
    const editPrescriptionAddedAtElement = $("#editPrescriptionAddedAt");

    $(
        "#prescriptionPatientId,#editPrescriptionPatientId,#filter_status,#prescriptionDoctorId,#editPrescriptionDoctorId,#prescriptionTime,#prescriptionMedicineCategoryId,#prescriptionMedicineBrandId,.prescriptionMedicineId,.prescriptionMedicineMealId,#editPrescriptionTime"
    ).select2({
        width: "100%",
    });

    $("#prescriptionMedicineBrandId, #prescriptionMedicineBrandId").select2({
        width: "100%",
        dropdownParent: $("#add_new_medicine"),
    });

    $("#prescriptionPatientId,#editPrescriptionPatientId").first().focus();

    if (prescriptionAddedAtElement.length) {
        $("#prescriptionAddedAt").flatpickr({
            maxDate: new Date(),
            locale: $(".userCurrentLanguage").val(),
        });
    }
    if (editPrescriptionAddedAtElement.length) {
        $("#editPrescriptionAddedAt").flatpickr({
            maxDate: new Date(),
            locale: $(".userCurrentLanguage").val(),
        });
    }
}

listenSubmit("#createPrescription, #editPrescription", function () {
    $(".btnPrescriptionSave").attr("disabled", true);
});

listenSubmit("#createMedicineFromPrescription", function (e) {
    e.preventDefault();
    $.ajax({
        url: $("#createMedicineFromPrescriptionPost").val(),
        method: "POST",
        data: $(this).serialize(),
        success: function (result) {
            displaySuccessMessage(result.message);
            $("#add_new_medicine").modal("hide");
            $(".medicineTable").load(location.href + " .medicineTable");
        },
        error: function (result) {
            printErrorMessage("#medicinePrescriptionErrorBox", result);
        },
    });
});

listenHiddenBsModal("#add_new_medicine", function () {
    resetModalForm(
        "#createMedicineFromPrescription",
        "#medicinePrescriptionErrorBox"
    );
});

const dropdownToSelecte2 = (selector) => {
    $(selector).select2({
        placeholder: Lang.get("js.select_medicine"),
        width: "100%",
    });
};
const dropdownToSelecteDuration2 = (selector) => {
    $(selector).select2({
        placeholder: Lang.get("js.select_duration"),
        width: "100%",
    });
};
const dropdownToSelecteInterVal = (selector) => {
    $(selector).select2({
        placeholder: Lang.get("js.select_dose_interval"),
        width: "100%",
    });
};

listenClick(".delete-prescription-medicine-item", function () {
    $(this).parents("tr").remove();
    // resetPrescriptionMedicineItemIndex()
});

listenClick(".add-medicine-btn", function () {
    let uniquePrescriptionId = $(".prescriptionUniqueId").val();

    let data = {
        medicines: JSON.parse($(".associatePrescriptionMedicines").val()),
        meals: JSON.parse($(".associatePrescriptionMeals").val()),
        doseDuration: JSON.parse($(".DoseDurationId").val()),
        doseInterVal: JSON.parse($(".DoseInterValId").val()),
        uniqueId: uniquePrescriptionId,
    };
    let prescriptionMedicineHtml = prepareTemplateRender(
        "#prescriptionMedicineTemplate",
        data
    );
    $(".prescription-medicine-container").append(prescriptionMedicineHtml);
    dropdownToSelecte2(".prescriptionMedicineId");
    dropdownToSelecte2(".prescriptionMedicineMealId");
    dropdownToSelecteDuration2(".DoseDurationIdTemplate");
    dropdownToSelecteInterVal(".DoseInterValIdTemplate");
    uniquePrescriptionId++;
    $(".prescriptionUniqueId").val(uniquePrescriptionId);

    // resetPrescriptionMedicineItemIndex();
});

const resetPrescriptionMedicineItemIndex = () => {
    let index = 1;
    if (index - 1 == 0) {
        let data = {
            medicines: JSON.parse($(".associatePrescriptionMedicines").val()),
            meals: JSON.parse($(".associatePrescriptionMeals").val()),
            doseDuration: JSON.parse($(".DoseDurationId").val()),
            doseInterVal: JSON.parse($(".DoseInterValId").val()),
            uniqueId: uniquePrescriptionId,
        };
        let packageServiceItemHtml = prepareTemplateRender(
            "#prescriptionMedicineTemplate",
            data
        );
        $(".prescription-medicine-container").append(packageServiceItemHtml);
        dropdownToSelecte2(".prescriptionMedicineId");
        dropdownToSelecte2(".prescriptionMedicineMealId");
        dropdownToSelecteDuration2(".DoseDurationIdTemplate");
        dropdownToSelecteInterVal(".DoseInterValIdTemplate");
        uniquePrescriptionId++;
    }
};

// listenKeyup('.prescription-dose',function (){

//     $.ajax({
//             type: 'get',
//             url: route('get-medicine',medicineId),
//             success: function (result) {
//                 $(salePriceId).val(result.data.selling_price.toFixed(2))
//                 $(buyPriceId).val(result.data.buying_price.toFixed(2))
//             },
//         });
//     });
