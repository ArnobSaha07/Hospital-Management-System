Livewire.hook("element.init", () => {
    $("#prescriptionHead").select2({
        width: "100%",
    });
});
listenClick('.delete-prescription-btn', function (event) {
    let prescriptionId = $(event.currentTarget).attr('data-id');
    deleteItem($('#indexPrescriptionUrl').val() + '/' + prescriptionId,
        '#prescriptionsTable',
        $('#prescriptionLang').val());
});

listenChange('.prescriptionStatus', function (event) {
    let prescriptionId = $(event.currentTarget).attr('data-id');
    prescriptionUpdateStatus(prescriptionId);
});

function prescriptionUpdateStatus(id) {
    $.ajax({
        url: $('#indexPrescriptionUrl').val() + '/' + +id + '/active-deactive',
        method: 'post',
        cache: false,
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                hideDropdownManually($('#prescriptionFilterBtn'), $('#prescriptionFilter'));
            }
        },
    });
}

listenClick('#prescriptionResetFilter', function () {
    $('#prescriptionHead').val('2').trigger('change');
    hideDropdownManually($('#prescriptionFilterBtn'), $('.dropdown-menu'));
});

listenClick('.show-prescription-btn', function (event) {
    event.preventDefault()
    let prescriptionId = event.currentTarget.dataset.id
    prescriptionRenderData(prescriptionId);
});

function prescriptionRenderData(id) {
    $.ajax({
        url: $('#prescriptionShowModal').val() + '/' + id,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#showPrescriptionPatientName').text(result.data.patient.patient_user.full_name);
                $('#showPrescriptionDoctorName').text(result.data.doctor.doctor_user.full_name);
                $('#showPrescriptionFoodAllergies').text(result.data.food_allergies);
                $('#showPrescriptionTendencyBleed').text(result.data.tendency_bleed);
                $('#showPrescriptionHeartDisease').text(result.data.heart_disease);
                $('#showPrescriptionHighBloodPressure').text(result.data.high_blood_pressure);
                $('#showPrescriptionDiabetic').text(result.data.diabetic);
                $('#showPrescriptionSurgery').text(result.data.surgery);
                $('#showPrescriptionAccident').text(result.data.accident);
                $('#showPrescriptionOthers').text(result.data.others);
                $('#showPrescriptionMedicalHistory').text(result.data.medical_history);
                $('#showPrescriptionCurrentMedication').text(result.data.current_medication);
                $('#showPrescriptionFemalePregnancy').text(result.data.female_pregnancy);
                $('#showPrescriptionBreastFeeding').text(result.data.breast_feeding);
                $('#showPrescriptionHealthInsurance').text(result.data.health_insurance);
                $('#showPrescriptionLowIncome').text(result.data.low_income);
                $('#showPrescriptionReference').text(result.data.reference);
                $('#showPrescriptionStatus').empty();
                if (result.data.status == 1) {
                    $('#showPrescriptionStatus').append(
                        '<span class="badge bg-light-success">'+Lang.get('js.active')+'</span>');
                } else {
                    $('#showPrescriptionStatus').append(
                        '<span class="badge bg-light-danger">'+Lang.get('js.deactive')+'</span>');
                }
                $('#showPrescriptionCreatedOn').text(moment(result.data.created_at).fromNow());
                $('#showPrescriptionUpdatedOn').text(moment(result.data.updated_at).fromNow());

                setValueOfEmptySpan();
                $('#showPrescription').appendTo('body').modal('show');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
}

listenChange('#prescriptionHead', function () {
    Livewire.dispatch('changeFilter',  {statusFilter: $(this).val()})
});

listenChange('.prescriptionMedicineId', function () {
    let medicineId = $(this).val();
    let uniqueId = $(this).attr('data-id');
    $.ajax({
        url: route('get-medicine-quantity', medicineId),
        method: 'get',
        cache: false,
        success: function (result) {
            if(result.data == null){
                $(".available-qty-div"+uniqueId).find("small").text("").end().css("margin-top", "0px");
                return false;
            }
            if (result.success) {
                if(result.data.available_quantity <= 10){
                    $('#availableQuantity'+uniqueId).text(
                        Lang.get('js.available_quantity')+ ' : ' +
                        result.data.available_quantity
                    ).addClass('text-danger').removeClass('text-success');
                    $('.available-qty-div').css({"margin-top":"22px"});
                }else{
                    $('#availableQuantity'+uniqueId).text(
                        Lang.get('js.available_quantity')+ ' : ' +
                        result.data.available_quantity
                    ).addClass('text-success').removeClass('text-danger');
                    $('.available-qty-div').css({"margin-top":"22px"});
                }
            }
        },
    });
});
