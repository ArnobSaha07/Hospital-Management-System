listenChange('#patients_prescription_filter_status', function () {
    Livewire.dispatch('changeFilter', {statusFilter: $(this).val()})
});
listenClick('#patientPrescriptionResetFilter', function () {
    $('#patients_prescription_filter_status').val(2).trigger('change')
    hideDropdownManually($('#dropdownMenuPrescription'), $('.dropdown-menu'))
});


