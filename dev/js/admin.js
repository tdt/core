
// Confirm deletions
$('.delete').on('click', function(){
    return window.confirm('Are you sure you want to delete this?');
});

// User edit
$('.btn.edit-user').on('click', function(e){
    e.preventDefault();

    // Get variables
    var row = $(this).parents('.dataset');
    var id = row.data('id');
    var name = row.data('name');
    var group = row.data('group');

    var modal = $('#editUser');
    $('#inputEditId').val(id);
    $('#inputEditName').prop('disabled', false).val(name);
    $('#inputEditGroup').prop('disabled', false);
    $('#inputEditGroup option[value="' + group + '"]').prop('selected', true);

    // Admin and everyone disable corresponding fields
    if(id < 3){
        $('#inputEditName').prop('disabled', true);
        $('#inputEditGroup').prop('disabled', true);

        if(id < 2){
            $('#inputEditPassword').prop('disabled', true);
        }
    }

    modal.modal('show');
});

// Tooltips
$('.hover-help').tooltip();
$('.hover-help').on('click', function(e){ e.preventDefault(); })