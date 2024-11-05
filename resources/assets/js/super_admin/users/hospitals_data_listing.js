// document.addEventListener('turbo:load', loadHospitalListingData)

Livewire.hook("element.init", () => {
    $("#statusArr,#roleArr").select2({
        width: "100%",
    });
});

function loadHospitalListingData() {

}

listenClick('#hospitalUserResetFilter', function () {
    $('#roleArr, #statusArr').val(0).trigger('change')
    hideDropdownManually($('#hospitalUserFilterButton'), $('.dropdown-menu'))
})

listenChange('#statusArr', function () {
    Livewire.dispatch('changeFilter', {statusFilter: $(this).val()})
})

listenChange('#roleArr', function () {
    Livewire.dispatch('changeRoleFilter', { roleFilter:$(this).val()})
})

$(document).on('contextmenu', '.user-impersonate', function (e) {
    e.preventDefault() // Stop right click on link
    return false
})

var control = false;
$(document).on('keyup keydown', function (e) {
    control = e.ctrlKey;
});

$(document).on('click', '.user-impersonate', function () {
    if (control) {
        return false; // Stop ctrl + click on link
    }
    let id = $(this).data('id');
    let element = document.createElement('a');
    element.setAttribute('href', $('#showUserIndexURL').val() + '/' + id + '/login');
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
    $('.user-impersonate').prop('disabled', true);
});
