<!DOCTYPE html>
<html>
    <head>
        <title>{{ $title }}</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="DC.title" content="{{ $title }}"/>
        <base target="_parent" />

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
        <link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}" />
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

            var track = new L.KML('<?php echo preg_replace("/'/", "\\'", $kml) ?>');
            var data = new L.FeatureGroup();
            for (i in track._layers) {
                data.addLayer(track._layers[i]);
            }
            data.addTo(map);
            map.fitBounds(data.getBounds(), {padding: [20, 20]});
        </script>
    </body>
</html>