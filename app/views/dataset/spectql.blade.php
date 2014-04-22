@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <pre class="prettyprint linenums:1 lang-js ">{{{ $body }}}</pre>
    </div>

    <div class="col-sm-3">
        <ul class="list-group">
            <li class="list-group-item no-padding">
                <a href="{{ $dataset_link }}:json{{ $query_string }}" class="btn btn-block btn-primary"><i class='fa fa-file-text-o'></i> View as JSON</a>
            </li>
            <li class="list-group-item">
                <h5 class="list-group-item-heading">SpectQL result</h5>
                <p class="list-group-item-text">
                    This data is the result of a SpectQL query
                </p>
                <br/>
                <a href='{{ \URL::to($definition['collection_uri'] . "/" . $definition['resource_name']) }}' class='btn'>View the original dataset</a>
            </li>
        </ul>
    </div>

@stop