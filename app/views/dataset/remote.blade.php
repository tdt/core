@extends('layouts.master')

@section('content')

<div class="col-sm-7 col-lg-8">
    <div data-rdftohtml-plugin='map'></div>
    <div data-rdftohtml-plugin='ontology'></div>
    <div data-rdftohtml-plugin='triples'></div>
</div>

<div class="col-sm-5 col-lg-4">
    <ul class="list-group">
        @if(!empty($source_definition['description']))
            <li class="list-group-item">
                <h5 class="list-group-item-heading">{{ trans('htmlview.description') }}</h5>
                <p class="list-group-item-text">
                    {{ $source_definition['description'] }}
                </p>
            </li>
        @endif
        <li class="list-group-item">
            <h5 class="list-group-item-heading">{{ trans('htmlview.source_type') }}</h5>
            <p class="list-group-item-text">
                {{ strtoupper($source_definition['type']) }}
            </p>
        </li>
    </ul>
    <div id="map" style="display: none;"></div>
</div>

<style>
#map { width:100%; height: 200px;min-height: 200px; background: blue;margin-top: 20px; }
@media (min-height: 500px) {
    #map {height: 300px;}
}
</style>
<link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}" />
<script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
<script textype="text/javascript" src='{{ URL::to("js/rdf2html.min.js") }}'></script>
<script type="text/javascript">
var dcat = {{json_encode($source_definition['dcat'])}}
var config = {
    plugins: ['triples', 'map', 'ontology', 'paging']
};
rdf2html(dcat, config);
</script>
<script type="text/javascript" src='{{ URL::to("js/n3-browser.min.js") }}'></script>
<script type="text/javascript">
var dcat = {{json_encode($source_definition['dcat'])}}
var parser = N3.Parser();
var triples = [];
parser.parse(dcat, function (error, triple, prefixes) {
    if (triple) {
        triples.push(triple)
    } else {
        parsed()
    }
});
function parsed () {
    for (var i = 0; i < triples.length; i++) {
        if (triples[i].predicate === 'http://www.w3.org/ns/locn#geometry') {
                console.log(N3.Util.getLiteralValue(triples[i].object))
            try {
                var json = JSON.parse(N3.Util.getLiteralValue(triples[i].object));
                showMap(json);
                break;
            } catch (e) {}
        }
    }
}
function showMap (json) {
    var feature = L.geoJson(json)
    document.querySelector('#map').style = '';
    var map = L.map('map');
    L.tileLayer('//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        minZoom: 3
    }).addTo(map);
    feature.addTo(map);
    map.fitBounds(feature.getBounds());
}
</script>

@stop