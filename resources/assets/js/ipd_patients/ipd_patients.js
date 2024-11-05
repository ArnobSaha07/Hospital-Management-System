Livewire.hook("element.init", () => {
    $("#ipd_patients_filter_status").select2({
        width: "100%",
    });
});

listenChange('#ipd_patients_filter_status', function () {
    Livewire.dispatch('changeFilter', { statusFilter: $(this).val() })
})

listenClick('#ipdResetFilter', function () {
    $('#ipd_patients_filter_status').val('0').trigger('change')
    hideDropdownManually($('#ipdPatientDepartmentFilterBtn'),
        $('.dropdown-menu'))
})

listen('click', '.deleteIpdDepartmentBtn', function (event) {
    let ipdPatientId = $(event.currentTarget).attr('data-id')
    deleteItem($('#indexIpdPatientUrl').val() + '/' + ipdPatientId,
        '', $('#ipdPatientLang').val())
})
