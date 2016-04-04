@extends('layouts.master')

@section('content')

<div class="col-sm-9">
    <h3>This is a harvested dataset</h3>
    <div data-rdftohtml-plugin='map'></div>

    <div data-rdftohtml-plugin='ontology'></div>

    <div data-rdftohtml-plugin='triples'></div>

    <span>
        The meta-data has been harvested from <a href="{{ $source_definition['dataset_uri'] }}">{{ $source_definition['dataset_uri'] }}</a>.
    </span>
    <pre>{{json_encode($definition, JSON_PRETTY_PRINT)}}
    </pre>

    <style>
        #map { width:100%; height: 200px;min-height: 200px; background: blue;margin-top: 20px; }
        @media (min-height: 500px) {
            #map {height: 300px;}
        }
    </style>
    <div id="map"></div>
</div>


<div class="col-sm-3">
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
        @if(!empty($definition['rights']))
            <li class="list-group-item">
                <h5 class="list-group-item-heading">{{ trans('htmlview.license') }}</h5>
                <p class="list-group-item-text">
                @if (!empty($definition['rights_uri']) && filter_var($definition['rights_uri'], FILTER_VALIDATE_URL))
                    <a href="{{ $definition['rights_uri'] }}">{{ $definition['rights'] }}</a>
                @else
                    {{ $definition['rights'] }}
                @endif
                </p>
            </li>
        @endif
        @if(!empty($definition['contact_point']))
            <li class="list-group-item">
                <h5 class="list-group-item-heading">{{ trans('htmlview.contact') }}</h5>
                <p class="list-group-item-text">
                @if(filter_var($definition['contact_point'], FILTER_VALIDATE_URL))
                    <a href="{{ $definition['contact_point'] }}">{{ $definition['contact_point'] }}</a>
                @else
                    {{ $definition['contact_point'] }}
                @endif
                </p>
            </li>
        @endif
        @if(!empty($definition['publisher_name']))
            <li class="list-group-item">
                <h5 class="list-group-item-heading">{{ trans('htmlview.publisher') }}</h5>
                <p class="list-group-item-text">
                    @if(!empty($definition['publisher_uri']) && filter_var($definition['publisher_uri'], FILTER_VALIDATE_URL))
                        <a href="{{ $definition['publisher_uri'] }}">{{ $definition['publisher_name'] }}</a>
                    @else
                        {{ $definition['publisher_name'] }}
                    @endif
                </p>
            </li>
        @endif
        @if(!empty($definition['keywords']))
            <li class="list-group-item">
                <h5 class="list-group-item-heading">{{ trans('htmlview.keywords') }}</h5>
                <p class="list-group-item-text">
                    {{ $definition['keywords'] }}
                </p>
            </li>
        @endif
    </ul>
</div>

<link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}" />
<script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
<script textype="text/javascript" src='{{ URL::to("js/rdf2html.min.js") }}'></script>
<script type="text/javascript">
var triples = {{ json_encode($body) }};
var config = {
    plugins: ['triples', 'map', 'ontology', 'paging']
};
rdf2html(triples, config);
</script>


@if (isset($definition['spatial']))
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
<link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}?v=1.0" />
<script>
var geo = {{json_encode($definition['spatial']['geometries'])}};

var map = L.map('map').setView([51,3], 7);

// Create a group with all features
for (var i = 0; i < geo.length; i++) {
    if (geo[i].type === 'geojson') {
        L.geoJson(JSON.parse(geo[i].geometry)).addTo(map);
    }
}

// declaring the group variable  
var group = new L.featureGroup;
$.each(map._layers, function(ml){
    console.log(map._layers, ml)
    if(map._layers[ml].feature) {
        group.addLayer(this)
    }
})
map.fitBounds(group.getBounds());
L.tileLayer('//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    minZoom: 3
}).addTo(map);
</script>
@endif

@stop