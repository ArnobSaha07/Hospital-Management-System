'use strict';

// document.addEventListener('turbo:load', loadPatientPrescriptionDate)

Livewire.hook("element.init", () => {
    $("#patient_id,#filter_status,#patients_prescription_filter_status").select2({
        width: "100%",
    });
    loadPatientPrescriptionDate();
});

function loadPatientPrescriptionDate() {
    $('#patient_id,#filter_status').select2({
        width: '100%',
    });
}
