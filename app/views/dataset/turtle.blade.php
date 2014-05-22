@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <div data-rdftohtml-plugin='map'></div>

        <div data-rdftohtml-plugin='ontology'></div>

        <div data-rdftohtml-plugin='triples'></div>
    </div>

    <div class="col-sm-3">
        @include('dataset/partials/details')
    </div>


    <script id="turtle" type="text/turtle">
        {{ $body }}
    </script>
    <link rel="stylesheet" href="{{ URL::to("css/leaflet.css") }}" />
    <script type="text/javascript" src='{{ URL::to("js/leaflet.min.js") }}'></script>
    <script textype="text/javascript" src='{{ URL::to("js/rdf2html.min.js") }}'></script>
    <script type="text/javascript">
        var triples = document.getElementById("turtle").innerHTML;
        var config = {
            plugins: ['triples', 'map', 'ontology', 'paging']
        };
        rdf2html(triples, config);
    </script>
@stop

@section('navigation')
    @include('dataset/partials/pagination')
@stop