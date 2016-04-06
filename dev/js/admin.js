
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

// Group edit
$('.btn.edit-group').on('click', function(e){
    e.preventDefault();

    // Get variables
    var row = $(this).parents('.dataset');
    var id = row.data('id');
    var name = row.data('name');

    var modal = $('#editGroup');
    $('#inputEditId').val(id);
    $('#inputEditName').val(name);

    modal.modal('show');
});

// Permissions toggle
$('.permissions').hide();
$('.edit-permissions').on('click', function(e){
    $(this).next('.permissions').toggle();
});

// Tooltips
$('.hover-help').tooltip();
$('.hover-help').on('click', function(e){ e.preventDefault(); });


$('form.add-dataset .identifier #input_collection, form.add-dataset .identifier #input_resource_name').on({
        'keydown': buildURI,
        'keyup': buildURI
});

function buildURI(e){
    var collection_uri = $('#input_identifier_display').data('url');
    var identifier = $('#input_collection').val() + '/' + $('#input_resource_name').val();
    $('#input_identifier_display').html(collection_uri + identifier);
    $('#input_identifier').val(identifier);
}


// Add dataset
$('.btn-add-dataset').on('click', function(e){
    e.preventDefault();

    // Get form variables (of active tab)
    var form = $('form.add-dataset');
    var tab_pane = $('.tab-pane.active');

    var mediatype = tab_pane.data('mediatype');

    // Loop through fields
    var data = {};
    var collection = $('#input_identifier', form).val();
    $('input, textarea, select', tab_pane).each(function(){
        if($(this).attr('name')){
            if($(this).attr('type') == 'checkbox'){
                data[$(this).attr('name')] = $(this).prop('checked') ? 1 : 0;
            }else{
                data[$(this).attr('name')] = $(this).val();
            }
        }
    });
    console.log(data);

    // Ajax call
    $.ajax({
        url: baseURL + 'api/definitions/' + collection,
        data: JSON.stringify(data),
        method: 'PUT',
        headers: {
            'Accept' : 'application/json',
            'Content-Type': 'application/tdt.definition+json',
            'Authorization': authHeader
        },
        success: function(e){
            // Done, redirect to datets page
            window.location = baseURL + 'api/admin/datasets';
        },
        error: function(e){
            if(e.status != 405){
                var error = JSON.parse(e.responseText);
                if(error.error && error.error.message){
                    $('.error .text', tab_pane).html(error.error.message);
                    $('.error', tab_pane).removeClass('hide').show().focus();
                }
            }else{
                // Ajax followed location header -> ignore
                window.location = baseURL + 'api/admin/datasets';
            }
        }
    });
});

// Add dataset
$('.btn-edit-dataset').on('click', function(e){
    e.preventDefault();

    // Get form variables
    var form = $('form.edit-dataset');
    var mediatype = form.data('mediatype');
    var identifier = form.data('identifier');

    // Loop through fields
    var data = {};
    var collection = '';
    $('input, textarea, select', form).each(function(){
        if($(this).attr('name')){
            if($(this).attr('type') == 'checkbox'){
                data[$(this).attr('name')] = $(this).prop('checked') ? 1 : 0;
            }else{
                data[$(this).attr('name')] = $(this).val();
            }
        }
    });

    // Ajax call
    $.ajax({
        url: baseURL + 'api/definitions/' + identifier,
        data: JSON.stringify(data),
        method: 'POST',
        headers: {
            'Accept' : 'application/json',
            'Authorization': authHeader
        },
        success: function(e){
            // Done, redirect to datets page
            window.location = baseURL + 'api/admin/datasets';
        },
        error: function(e){
            if(e.status != 405){
                var error = JSON.parse(e.responseText);
                if(error.error && error.error.message){
                    $('.error .text').html(error.error.message);
                    $('.error').removeClass('hide').show().focus();
                }
            }else{
                // Ajax followed location header -> ignore
                window.location = baseURL + 'api/admin/datasets';
            }
        }
    });
});

// Load google maps for GeoDCAT
$('.location-picker').one('click', function(e) {
    $(this).height('300px').removeClass('btn').removeClass('btn-default')
    var input = $('#' + $(this).data('id'));
    var rectangle, infoWindow, map;
    var leaflet = document.createElement('script');
    leaflet.onload = function () {
        map = new google.maps.Map(document.querySelector('.location-picker'), {
            mapTypeControl: false,
            streetViewControl: false,
            center: {lat: 50, lng: 10},
            zoom: 2
        });

        var bounds = {
            north: 70,
            south: 35,
            east: 40,
            west: -10
        };

        // Define a rectangle and set its editable property to true.
        infoWindow = new google.maps.InfoWindow();
        rectangle = new google.maps.Rectangle({
            bounds: bounds,
            draggable: true,
            editable: true
        });
        rectangle.addListener('bounds_changed', boundsChanged);
        boundsChanged()
        rectangle.setMap(map);
    }
    leaflet.setAttribute('type', 'text/javascript');
    leaflet.setAttribute('src', 'https://maps.googleapis.com/maps/api/js');
    document.head.appendChild(leaflet);

    var boundsChanged = function (event) {
        var ne = rectangle.getBounds().getNorthEast();
        var sw = rectangle.getBounds().getSouthWest();

        var contentString = 'North-east corner: ' + ne.lat() + ', ' + ne.lng() + '<br>South-west corner: ' + sw.lat() + ', ' + sw.lng();
        console.log('geojson', ne.lat() , ne.lng() , sw.lat() , sw.lng())
        input.attr('value', JSON.stringify({
            type: 'Polygon',
            coordinates: [
                [
                    [ne.lat(), sw.lng()],
                    [ne.lat(), ne.lng()],
                    [sw.lat(), ne.lng()],
                    [sw.lat(), sw.lng()],
                    [ne.lat(), sw.lng()]
                ]
            ]
        }));

        // Set the info window's content and position.
        infoWindow.setContent(contentString);
        infoWindow.setPosition(ne);
        infoWindow.open(map);
    }
});

// IntroJS
$('.introjs').on('click', function(e){
    e.preventDefault();
    introJs().start();
});
