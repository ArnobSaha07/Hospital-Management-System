document.addEventListener('turbo:load', loadRadiologyTestCreateEditData)

function loadRadiologyTestCreateEditData() {

    $('.price-input').trigger('input');

    $('#radiologyCategories,#chargeCategory,#editCategoryName,#editChargeCategory,#chargeCode').select2({
        width: '100%',
    });

}

$('#createRadiologyTest, #editRadiologyTest').
    find('input:text:visible:first').
    focus();

    listen('change', '.charge-category', function (event) {
        let chargeCategoryId = $(this).val();
        (chargeCategoryId !== '')
            ? getRadiologyChargeCode(chargeCategoryId)
            : $('#chargeCode').empty(),
            $('#chargeCode').attr('disabled',true),
            $('#chargeCode').append($('<option></option>').text(Lang.get('js.select_charge'))),$(".rd-test-standard-charge").val('');
    });

    window.getRadiologyChargeCode = function (id) {
        $.ajax({
            url: $('.radiology-test-url').val() + '/get-charge-code' + '/' + id,
            method: 'get',
            cache: false,
            success: function (result) {
                if (result.data !== '') {
                    $('#chargeCode').empty()
                    $('#chargeCode').removeAttr('disabled')
                    $('#chargeCode').append($('<option></option>').text(Lang.get('js.select_charge')))
                    $.each(result.data, function (i, v) {
                        $('#chargeCode').
                            append($('<option></option>').
                                attr('value', i).
                                text(v))
                    })
                }else {
                    $('#chargeCode').append($('<option></option>').
                        text(Lang.get('js.select_charge')))
                }
            },
        });
    };

listen('change', '.charge-code', function (event) {
    let chargeId = $(this).val();
    (chargeId !== '')
        ? getRadiologyStandardCharge(chargeId)
        : $('.rd-test-standard-charge').val('')
});

window.getRadiologyStandardCharge = function (id) {
    $.ajax({
        url: $('.radiology-test-url').val() + '/get-standard-charge' + '/' + id,
        method: 'get',
        cache: false,
        success: function (result) {
            if (result !== '') {
                $('.rd-test-standard-charge').val(result.data)
                $('.price-input').trigger('input')
            }
        },
    });
};
