window.addEventListener("turbo:load", loadAddCustomFieldData);

function loadAddCustomFieldData() {
    if (!$("#module_name").length) {
        return;
    }

    $("#module_name").select2({
        dropdownParent: $("#add_custom_field_modal"),
    });

    $("#edit_module_name").select2({
        dropdownParent: $("#edit_custom_field_modal"),
    });

    if (!$("#field_type").length ) {
        return;
    }

    $("#field_type").select2({
        dropdownParent: $("#add_custom_field_modal"),
    });
    $("#edit_field_type").select2({
        dropdownParent: $("#edit_custom_field_modal"),
    });
}

listenChange($('#field_type'), function () {
    var fieldType = $('#field_type').val();
    if (fieldType == 4 || fieldType == 5) {
        $('.fieldValue').removeClass('d-none');
    } else {
        $('.fieldValue').addClass('d-none');
    }
})

listenSubmit("#addCustomFieldForm", function (event) {
    event.preventDefault();
    var values = $('#values').val().replace(/[;\-!@£$%^&*()_={}<>.,]+/g, ' ');
    var field_type = $('#field_type').val();
    if(values.split(' ').length == 1 && (field_type == 4 || field_type == 5) ){
        displayErrorMessage(Lang.get('js.value_must_be_greter_then'))
    }else{
        $('#values').val(replaceSpacesWithCommas($('#values').val()));
        var data = $(this).serialize();
        $.ajax({
            url: route("custom-fields.store"),
            type: "POST",
            data: data,
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $("#addCustomFieldForm")[0].reset();
                    $("#add_custom_field_modal").modal("hide");
                    Livewire.dispatch("refresh");
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
        });
    }

});

listenHiddenBsModal("#add_custom_field_modal", function () {
    resetModalForm("#addCustomFieldForm", "#addCustomFieldErrorsBox");
    $("#module_name").val("").trigger("change.select2").select2('close');
    $("#field_type").val("").trigger("change.select2").select2('close');
});

listenClick(".updateCustomFieldBtn", function (event) {
    let id = $(event.currentTarget).attr('data-id');;

    $.ajax({
        url: route("custom-fields.edit", id),
        type: "GET",
        success: function (result) {
            var data = result.data;
            if (result.success) {
                $('#editFieldId').val(data.id);
                $('#edit_module_name').val(data.module_name).trigger("change.select2");
                $('#edit_field_type').val(data.field_type).trigger("change.select2");
                $('#edit_field_name').val(data.field_name);
                $('#edit_grid').val(data.grid);
                $('#edit_values').val(data.values);
                if (data.is_required == 0) {
                    $('#edit_is_reqired').val(0).prop('checked', false);
                } else {
                    $('#edit_is_reqired').val(1).prop('checked', true);
                }
                if (data.field_type == 4 || data.field_type == 5) {
                    $('.EditFieldValue').removeClass('d-none');
                } else {
                    $('.EditFieldValue').addClass('d-none');
                }
            }
        },
    });
});


listenChange($('#edit_field_type'), function () {
    var fieldType = $('#edit_field_type').val();
    if (fieldType == 4 || fieldType == 5) {
        $('.EditFieldValue').removeClass('d-none');
    } else {
        $('.EditFieldValue').addClass('d-none');
    }
})

listenSubmit('#editCustomFieldForm', function (e) {
    e.preventDefault()
    var edit_values = $('#edit_values').val().replace(/[;\-!@£$%^&*()_={}<>,]+/g, ' ');
    var edit_field_type = $('#edit_field_type').val();
    if(edit_values.split(' ').length == 1 && (edit_field_type == 4 || edit_field_type == 5) ){
        displayErrorMessage(Lang.get('js.value_must_be_greter_then'));
    }else{
        $('#edit_values').val(replaceSpacesWithCommas($('#edit_values').val()));
        var id = $('#editFieldId').val();
        $.ajax({
            url: $('#indexAddCustomFieldURL').val() + '/' + id,
            type: 'put',
            data: $(this).serialize(),
            success: function (result) {
                if (result.success) {
                    displaySuccessMessage(result.message);
                    $('#edit_custom_field_modal').modal('hide');
                    Livewire.dispatch('refresh');
                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
        });
    }

})

listenHiddenBsModal('#edit_custom_field_modal', function () {
    resetModalForm('#editCustomFieldForm','#editCustomFieldErrorsBox');
    $("#edit_module_name").val("").trigger("change.select2").select2('close');
    $("#edit_field_type").val("").trigger("change.select2").select2('close');
});

listenClick('.custom-field-delete-btn', function (event) {
    let fieldId = $(event.currentTarget).attr('data-id');
    deleteItem($('#indexAddCustomFieldURL').val() + '/' + fieldId, '',
        $('#customField').val());
});

function replaceSpacesWithCommas(value) {
    // var result = value.replace(/[ ;\-!@£$%^&*()_={}<>]+/g, ',');
    var result = value.replace(/[^a-zA-Z0-9]+/g, ',');
    result = result.replace(/,+$/, '');
    result = result.trim();
    return result;
}
