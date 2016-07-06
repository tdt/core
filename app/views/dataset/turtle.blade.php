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
    <link rel="stylesheet" href="{{ asset("css/leaflet.css", Config::get('ssl_enabled')) }}" />
    <script type="text/javascript" src='{{ asset("js/leaflet.min.js", Config::get('ssl_enabled')) }}'></script>
    <script textype="text/javascript" src='{{ asset("js/rdf2html.min.js", Config::get('ssl_enabled')) }}'></script>
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
