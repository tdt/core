@extends('layouts.master')

@section('content')

    <div class='map-container'>
        <iframe src='{{ $body }}'></iframe>
    </div>

    <div class="col-sm-9">
    </div>

    <div class="col-sm-3">
        @include('dataset/partials/details')
    </div>
@stop
