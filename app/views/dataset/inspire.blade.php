@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <h3>This is a harvested dataset</h3>
    </div>

    <div class="col-sm-9">
        <span>
            The meta-data has been harvested from an ISO INSPIRE document. TODO: provide download link for the document
        </span>
    </div>

    <div class="col-sm-3">
        @include('dataset/partials/details')
    </div>
@stop