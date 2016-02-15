<!DOCTYPE html>
<html>
    <head>
        <title>{{ $title }}</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="DC.title" content="{{ $title }}"/>
        <base target="_parent" />

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
        <link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}?v=1.0" />
        <style>
            body { margin:0; padding:0; }
            #map { position:absolute; top:0; bottom:0; width:100%; }
        </style>
    </head>
    <body>
        <div id='map'></div>
        <script>

            var map = L.map('map').setView([51,3], 7);
            L.tileLayer('//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
                minZoom: 3
            }).addTo(map);

            @if ($type == 'geojson')
                $.getJSON('{{ $url }}', function(data){
                    var geoData = L.geoJson(data, {
                        onEachFeature: function (feature, layer) {
                            var popup = '<strong>{[name]}</strong><br/>{[description]}<br/>';

                            $.each(layer.feature.properties, function (key, val) {
                                if (key != 'name' && key != 'description') {
                                    popup += "<br/><strong>" + key + "</strong>: " + val;
                                } else {
                                    popup = popup.replace('{[' + key + ']}', val);
                                }
                            });

                        // Replace when no data was provided
                        popup = popup.replace('<strong>{[name]}</strong><br/>', '');
                        popup = popup.replace('{[description]}<br/><br/>', '');

                        layer.bindPopup(popup);
                    }
                }).addTo(map);

                    map.fitBounds(geoData.getBounds(), {padding: [20, 20]});
                });
            @elseif ($type == 'kml')
                var bounds = {};
                var data = omnivore.kml('{{ $url }}')
                .on('ready', function() {
                    data.eachLayer(function(layer) {
                        var popup = '';
                        $.each(layer.feature.properties, function (key, val) {
                            if (key != 'name' && key != 'description') {
                                popup += "<strong>" + key + ": " + val + "</strong>\n";
                            }
                        });
                        layer.bindPopup(popup);
                    });
                    map.fitBounds(data.getBounds(), {padding: [20, 20]});
                })
                .addTo(map);
            @endif
        </script>
    </body>
</html>