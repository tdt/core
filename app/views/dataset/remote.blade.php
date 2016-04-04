@extends('layouts.master')

@section('content')

<div class="col-sm-9">
    <h4 class="subject"><a href="http://demo.ckan.org/dataset/3d024d55-5cd9-4593-81cf-7ec9fec949a8">http://demo.ckan.org/dataset/3d024d55-5cd9-4593-81cf-7ec9fec949a8</a></h4>
    <table class="triples table table-hover">
        <tbody>
        @foreach($definition as $key => $value)
            @if(isset($body['properties'][$key]) && !empty($value))
            <tr>
                <td>{{$key}}</td>
                <td>{{$value}}</td>
            </tr>
            @endif
        @endforeach
        </tbody>
    </table>
    <div id="map" style="display: none;"></div>
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
    </ul>
</div>
@if (isset($definition['spatial']))
<style>
#map { width:100%; height: 200px;min-height: 200px; background: blue;margin-top: 20px; }
@media (min-height: 500px) {
    #map {height: 300px;}
}
</style>
<script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
<link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}?v=1.0" />
<script>
// GeoJSON to map feature
var feature, geo = {{json_encode($definition['spatial']['geometries'])}};
for (var i = 0; i < geo.length; i++) {
    if (geo[i].type === 'geojson') {
        feature = L.geoJson(JSON.parse(geo[i].geometry))
    }
}
if (!feature) {
    throw new Error('Not spatial after all..');
}

// Show map
document.querySelector('#map').style = '';
var map = L.map('map');
L.tileLayer('//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    minZoom: 3
}).addTo(map);
feature.addTo(map);
map.fitBounds(feature.getBounds());
</script>
@endif

@stop