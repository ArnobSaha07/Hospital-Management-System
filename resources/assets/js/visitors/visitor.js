// document.addEventListener('turbo:load', loadVisitorData)
Livewire.hook("element.init", () => {
    $("#visitorsHead").select2({
        width: "100%",
    });
    loadVisitorData();
});
function loadVisitorData() {
    if (!$('#purposeArr').length) {
        return
    }

    $('#purposeArr').select2({
        width: '100%',
    });

}

listenClick('.delete-visitor-btn', function (event) {
    event.preventDefault();
    let visitorId = $(event.currentTarget).attr('data-id');
    deleteItem($('.visitorUrl').val() + '/' + visitorId, '#visitorTbl', $('#visitorLang').val());
});

listenClick('#visitorResetFilter', function () {
    $('#visitorsHead').val(0).trigger('change');
    hideDropdownManually($('#visitorsFilterBtn'), $('.dropdown-menu'));
});
listenChange('#visitorsHead', function () {
    Livewire.dispatch('changeFilter', {statusFilter: $(this).val()})
});
