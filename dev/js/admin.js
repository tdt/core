
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

// Add attribution to dataset
$('.btn-attribution').on('click', function(e){
    var parent = $(e.target).parent().parent().parent();
    var role = JSON.parse($(e.target).next().val());
    var tpl = $('#person').html().replace('#ROLE#', role.name).replace('#DESC#', role.desc).replace('#OPTION#', role.option);
    parent.append(tpl);
});

// Remove attribution to dataset
$('.attribution-person .btn-delete').on('click', function(e){
    $(this).closest('.attribution-person').remove();
});

// Autofill http for URLs
$('input[name=publisher_uri], input[name=publisher_uri]').on('blur', function(e){
    var val = $(this).val();
    if (val && val.slice(0, 6) !== 'http:/' && val.slice(0, 6) !== 'https:') {
        $(this).val('http://' + val);
    }
});

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
                if (data[$(this).attr('name')] == 'profile') {
                    if ($(this).prop('checked')) {
                        data[$(this).attr('name')] = $(this).val();
                    }
                } else {
                    data[$(this).attr('name')] = $(this).val();
                }
            }
        }
    });

    //<--- -----feature 2 ---->

    data.to_be_indexed = "0";
    var to_be_indexed = $('.form-control').find('.indexed').val();
    data.to_be_indexed = to_be_indexed;

    $('.attribution-person', tab_pane).each(function(){
        if (!data.attribution) {
            data.attribution = [];
        }
        var name = $(this).find('.name').val();
        var email = $(this).find('.email').val();
        if (name || email) {
            data.attribution.push({
                role: $(this).data('role'),
                name: name,
                email: email
            })
        }
    });

    // Upload the file if set
    if( $(".tab-pane.active").find("#fileupload").length == 0 || $(".tab-pane.active").find("#fileupload")[0].files.length == 0 ){
        console.log("no files selected");
        $.ajax({
          url: baseURL + "api/definitions/" + e,
          data: JSON.stringify(d),
          method: "PUT",
          headers: {
             Accept: "application/json",
             "Content-Type": "application/tdt.definition+json",
             Authorization: authHeader
         },
         success: function(a) {
             window.location = baseURL + "api/admin/datasets"
         },
         error: function(a) {
             if (405 != a.status) {
                var b = JSON.parse(a.responseText);
                b.error && b.error.message && ($(".error .text", c).html(b.error.message), $(".error", c).removeClass("hide").show().focus())
            } else window.location = baseURL + "api/admin/datasets"
        }
    })
    } else {

        var file = $(".tab-pane.active").find('input[type=file]')[0].files[0];
        var fd = new FormData();
        fd.append("fileupload", file);

        $.ajax({
            async: true,
            type: "POST",
                    contentType: false,
                    url: '/upload-file',
                    data: fd,
                    processData: false,
                    success: function (data) {
                       d["fileupload"] = data;

                       $.ajax({
                          url: baseURL + "api/definitions/" + e,
                          data: JSON.stringify(d),
                          method: "PUT",
                          headers: {
                             Accept: "application/json",
                             "Content-Type": "application/tdt.definition+json",
                             Authorization: authHeader
                         },
                         success: function(a) {
                             window.location = baseURL + "api/admin/datasets"
                         },
                         error: function(a) {
                             if (405 != a.status) {
                                var b = JSON.parse(a.responseText);
                                b.error && b.error.message && ($(".error .text", c).html(b.error.message), $(".error", c).removeClass("hide").show().focus())
                            } else window.location = baseURL + "api/admin/datasets"
                        }
                    });
                   },
                   timeout: 10000
               });
    }
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
                if ($(this).attr('name') == 'profile') {
                    if ($(this).prop('checked')) {
                        data[$(this).attr('name')] = $(this).val();
                    }
                } else {
                    data[$(this).attr('name')] = $(this).val();
                }
            }
        }
    });

    // Upload a file
    if( $("form.edit-dataset").find("#fileupload").length == 0 || $("form.edit-dataset").find("#fileupload")[0].files.length == 0 ){
        console.log("no files selected");
        $.ajax({
          url: baseURL + "api/definitions/" + c,
          data: JSON.stringify(d),
          method: "POST",
          headers: {
             Accept: "application/json",
             Authorization: authHeader
         },
         success: function(a) {
             window.location = baseURL + "api/admin/datasets"
         },
         error: function(a) {
             if (405 != a.status) {
                var b = JSON.parse(a.responseText);
                b.error && b.error.message && ($(".error .text").html(b.error.message), $(".error").removeClass("hide").show().focus())
            } else window.location = baseURL + "api/admin/datasets"
        }
    })
    } else {

        var file = $("form.edit-dataset").find('input[type=file]')[0].files[0];
        var fd = new FormData();
        fd.append("fileupload", file);

        $.ajax({
            async: true,
            type: "POST",
            contentType: false,
            url: '/upload-file',
            data: fd,
            processData: false,
            success: function (data) {
               d["fileupload"] = data;

               $.ajax({
                  url: baseURL + "api/definitions/" + c,
                  data: JSON.stringify(d),
                  method: "POST",
                  headers: {
                     Accept: "application/json",
                     Authorization: authHeader
                 },
                 success: function(a) {
                     window.location = baseURL + "api/admin/datasets"
                 },
                 error: function(a) {
                     if (405 != a.status) {
                        var b = JSON.parse(a.responseText);
                        b.error && b.error.message && ($(".error .text").html(b.error.message), $(".error").removeClass("hide").show().focus())
                    } else window.location = baseURL + "api/admin/datasets"
                }
            });
           },
           timeout: 10000
       });
    }

    $('.attribution-person').each(function(){
        if (!data.attribution) {
            data.attribution = [];
        }
        var name = $(this).find('.name').val();
        var email = $(this).find('.email').val();
        if (name || email) {
            data.attribution.push({
                role: $(this).data('role'),
                name: name,
                email: email
            })
        }
    });
});

// Load google maps for GeoDCAT
var mapScriptLoaded = false;
$('.location-picker').one('click', function(e) {
    $(this).height('300px').removeClass('btn').removeClass('btn-default')
    var pane = $(e.target).closest('.panel');
    var input = $('#' + $(this).data('id'), pane);
    var rectangle, infoWindow, map;
    var init = function () {
        map = new google.maps.Map($('.location-picker', pane).get(0), {
            mapTypeControl: false,
            streetViewControl: false,
            center: {lat: 50, lng: 10},
            zoom: 2
        });

        // Get current bounds or set default
        var bounds;
        try {
            var geo = JSON.parse(JSON.parse(input.val()));
            bounds = {
                north: geo.coordinates[0][0][1],
                south: geo.coordinates[0][2][1],
                east: geo.coordinates[0][2][0],
                west: geo.coordinates[0][0][0]
            }
        } catch (e) {
            bounds = {
                north: 70,
                south: 35,
                east: 40,
                west: -10
            }
        }

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

    var boundsChanged = function (event) {
        var ne = rectangle.getBounds().getNorthEast();
        var sw = rectangle.getBounds().getSouthWest();

        var contentString = 'North-east corner: ' + ne.lat() + ', ' + ne.lng() + '<br>South-west corner: ' + sw.lat() + ', ' + sw.lng();
        //console.log('geojson', ne.lat() , ne.lng() , sw.lat() , sw.lng())
        input.val(JSON.stringify({
            type: 'Polygon',
            coordinates: [
                [
                    [sw.lng(), ne.lat()],
                    [ne.lng(), ne.lat()],
                    [ne.lng(), sw.lat()],
                    [sw.lng(), sw.lat()],
                    [sw.lng(), ne.lat()]
                ]
            ]
        }));

        // Set the info window's content and position.
        infoWindow.setContent(contentString);
        infoWindow.setPosition(ne);
        infoWindow.open(map);
    }

    if (mapScriptLoaded) {
        init()
    } else {
        var tag = document.createElement('script');
        tag.onload = init;
        tag.setAttribute('type', 'text/javascript');
        tag.setAttribute('src', 'https://maps.googleapis.com/maps/api/js');
        document.head.appendChild(tag);
        mapScriptLoaded = true;
    }
});

// Profile selector > DCAT-AP vs GeoDCAT-AP
var selectProfile = function (profile, pane) {
    //console.log(profile, pane)
    if (profile == 'dcat') {
        $('.profile-geodcat', pane).hide()
        $('.profile-dcat', pane).show()
        pane.removeClass('geodcat-enabled')
    } else {
        $('.profile-dcat', pane).hide()
        $('.profile-geodcat', pane).show()
        pane.addClass('geodcat-enabled')
        $('.location-picker', pane).click()
    }
};
// Profile selector > Initial selection
selectProfile($('.profile-selector input[name=profile]:checked').val(), $('.panel-dcat'));
// Profile selector > Selection changed
$('.profile-selector').on('change', function(e){
    var pane = $(e.target).closest('.panel');
    selectProfile($(e.target).val(), pane)
});

// IntroJS
$('.introjs').on('click', function(e){
    e.preventDefault();
    introJs().start();
});

// Upload a file
if( $(".tab-pane.active").find("#fileupload").length == 0 || $(".tab-pane.active").find("#fileupload")[0].files.length == 0 ){
    console.log("no files selected");
    $.ajax({
      url: baseURL + "api/definitions/" + e,
      data: JSON.stringify(d),
      method: "PUT",
      headers: {
         Accept: "application/json",
         "Content-Type": "application/tdt.definition+json",
         Authorization: authHeader
     },
     success: function(a) {
           window.location = baseURL + "api/admin/datasets"
       },
       error: function(a) {
           if (405 != a.status) {
            var b = JSON.parse(a.responseText);
            b.error && b.error.message && ($(".error .text", c).html(b.error.message), $(".error", c).removeClass("hide").show().focus())
        } else window.location = baseURL + "api/admin/datasets"
    }
    })
} else {

    var file = $(".tab-pane.active").find('input[type=file]')[0].files[0];
    var fd = new FormData();
    fd.append("fileupload", file);

    $.ajax({
        async: true,
        type: "POST",
        contentType: false,
        url: '/upload-file',
        data: fd,
        processData: false,
        success: function (data) {
         d["fileupload"] = data;

         $.ajax({
          url: baseURL + "api/definitions/" + e,
          data: JSON.stringify(d),
          method: "PUT",
          headers: {
           Accept: "application/json",
           "Content-Type": "application/tdt.definition+json",
           Authorization: authHeader
       },
       success: function(a) {
           window.location = baseURL + "api/admin/datasets"
       },
       error: function(a) {
           if (405 != a.status) {
            var b = JSON.parse(a.responseText);
            b.error && b.error.message && ($(".error .text", c).html(b.error.message), $(".error", c).removeClass("hide").show().focus())
        } else window.location = baseURL + "api/admin/datasets"
    }
});
     },
     timeout: 10000
 });
}

if( $("form.edit-dataset").find("#fileupload").length == 0 || $("form.edit-dataset").find("#fileupload")[0].files.length == 0 ){
                console.log("no files selected");
                 $.ajax({
                  url: baseURL + "api/definitions/" + c,
                  data: JSON.stringify(d),
                  method: "POST",
                  headers: {
                   Accept: "application/json",
                   Authorization: authHeader
                  },
                  success: function(a) {
                   window.location = baseURL + "api/admin/datasets"
                  },
                  error: function(a) {
                   if (405 != a.status) {
                    var b = JSON.parse(a.responseText);
                    b.error && b.error.message && ($(".error .text").html(b.error.message), $(".error").removeClass("hide").show().focus())
                   } else window.location = baseURL + "api/admin/datasets"
                  }
                 })
            } else {

                var file = $("form.edit-dataset").find('input[type=file]')[0].files[0];
                var fd = new FormData();
                fd.append("fileupload", file);

                $.ajax({
                    async: true,
                    type: "POST",
                    //dataType: "json", // or html if you want...
                    contentType: false, // high importance!
                    url: '/upload-file', // you need change it.
                    data: fd, // high importance!
                    processData: false, // high importance!
                    success: function (data) {
                         d["fileupload"] = data;

                         $.ajax({
                          url: baseURL + "api/definitions/" + c,
                          data: JSON.stringify(d),
                          method: "POST",
                          headers: {
                           Accept: "application/json",
                           Authorization: authHeader
                          },
                          success: function(a) {
                           window.location = baseURL + "api/admin/datasets"
                          },
                          error: function(a) {
                           if (405 != a.status) {
                            var b = JSON.parse(a.responseText);
                            b.error && b.error.message && ($(".error .text").html(b.error.message), $(".error").removeClass("hide").show().focus())
                           } else window.location = baseURL + "api/admin/datasets"
                          }
                         });
                    },
                    timeout: 10000
                });
            }