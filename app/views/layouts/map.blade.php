<!DOCTYPE html>
<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
        <script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'>
        </script>
        <link rel="stylesheet" href="//cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
        <style>
            body { margin:0; padding:0; }
            #map { position:absolute; top:0; bottom:0; width:100%; }
        </style>
    </head>
    <body>
        <div id='map'></div>
        <script>
            var map = L.map('map').setView([51,3], 7);
            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors</a>',
                maxZoom: 18
            }).addTo(map);

            var track = new L.KML('<?php echo preg_replace("/'/", "\\'", $kml) ?>');
            var data = new L.FeatureGroup();
            for(i in track._layers){
                data.addLayer(track._layers[i]);
            }
            data.addTo(map);
            map.fitBounds(data.getBounds());
        </script>
    </body>
</html>