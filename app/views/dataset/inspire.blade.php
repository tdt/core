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
    <div id="geojson-map" style="display: none;"></div>
</div>

<link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}" />
<script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
<script textype="text/javascript" src='{{ URL::to("js/rdf2html.min.js") }}'></script>
<script type="text/javascript" src='{{ URL::to("js/n3-browser.min.js") }}'></script>
<script type="text/javascript">
var dcat = {{json_encode($source_definition['dcat'])}}

// Show rdf triples
var config = {
    plugins: ['triples', 'map', 'ontology', 'paging']
};
rdf2html(dcat, config);

// Show map based on parsed turtle
var store = N3.Store();
var parser = N3.Parser();
parser.parse(dcat, function (error, triple, prefixes) {
    if (triple) {
        return store.addTriple(triple);
    }
    var geo = store.find(null, 'http://www.w3.org/ns/locn#geometry', null);
    for (var i = 0; i < geo.length; i++) {
        try {
            var json = JSON.parse(N3.Util.getLiteralValue(geo[i].object));
            showGeoJsonMap(json);
            break;
        } catch (e) {}
    }
});
</script>

@stop
