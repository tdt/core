@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <pre class="prettyprint linenums:1 lang-js ">{{{ $body }}}</pre>
    </div>

    <div class="col-sm-3">
        <a href="{{ $dataset_link }}.json" class="btn btn-block btn-primary">View as JSON</a>
        <a href="{{ $dataset_link }}.xml" class="btn btn-block">View as XML</a>

        <br/>
        <ul class="list-group">
            <li class="list-group-item">
                <h5 class="list-group-item-heading">Collection</h5>
                <p class="list-group-item-text">
                    This URI is a collection, and can contain datasets and other collections.
                </p>
            </li>
        </ul>
    </div>

@stop