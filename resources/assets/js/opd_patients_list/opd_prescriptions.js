listenClick('.viewOpdPrescription', function () {
    $('#showOpdPrescriptionUrl').val() + '/' + $(this).data('id')
    $.ajax({
        url: $('#showOpdPrescriptionUrl').val() + '/' + $(this).data('id'),
        type: 'get',
        success: function (result) {
            $('#opdPrescriptionViewData').html(result);
            $('#showOpdPrescriptionModal').modal('show');
            ajaxCallCompleted();
        },
    });
});
