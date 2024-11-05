document.addEventListener('turbo:load', loadbedsData)

'use strict';

function loadbedsData() {
    const editBedTypeElement = $('#editBedType')

    if(editBedTypeElement.length){
        $('#editBedType').select2({
            width: '100%',
            dropdownParent: $('#edit_beds_modal')
        });
    }
}


listenHiddenBsModal('#edit_beds_modal', function () {
    resetModalForm('#EditBedsForm', '#editValidationErrorsBox');
});
