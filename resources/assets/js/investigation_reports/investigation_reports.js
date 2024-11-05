'use strict';

// document.addEventListener('turbo:load', loadInvestigationReportData)
Livewire.hook("element.init", () => {
    $("#filterInReportStatus").select2({
        width: "100%",
    });
    loadInvestigationReportData()
});
function  loadInvestigationReportData() {
    listenClick('#resetInReportFilter', function () {
        $('#filterInReportStatus').val(2).trigger('change');
        hideDropdownManually($('#investigationReportMenuButton'), $('.dropdown-menu'))
    });
    listenChange('#filterInReportStatus', function () {
        Livewire.dispatch('changeFilter', {statusFilter: $(this).val()})
    });
    listenClick('.delete-in-report-btn', function (event) {
        let investigationReportId = $(event.currentTarget).attr('data-id');
        deleteItem(
            $('#indexInvestigationReportUrl').val() + '/' +
            investigationReportId,
            '#investigationReportTable',
            $('#investigationReportLang').val(),
        );
    });
}
