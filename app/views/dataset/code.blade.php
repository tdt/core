@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <pre class="prettyprint linenums:1 lang-js ">{{{ $body }}}</pre>

        @if(!empty($paging))
            <ul class="pager">
                @if(!empty($prev_link))
                    <li class="previous">
                        <a href="{{ URL::to($dataset_link . $prev_link ) }}">&larr; Previous</a>
                    </li>
                @endif
                @if(!empty($next_link))
                    <li class="next">
                        <a href="{{ URL::to($dataset_link . $next_link) }}">Next &rarr;</a>
                    <li>
                @endif
            </ul>
        @endif
    </div>

    <div class="col-sm-3">
        <a href="{{ $dataset_link }}.json{{ $query_string }}" class="btn btn-block btn-primary"><i class='fa fa-file-text-o'></i> View as JSON</a>
        <a href="{{ $dataset_link }}.xml{{ $query_string }}" class="btn btn-block"><i class='fa fa-code'></i> View as XML</a>


        <br/>
        <ul class="list-group">
            <li class="list-group-item">
                <h5 class="list-group-item-heading">Documentation</h5>
                <p class="list-group-item-text">
                    {{ $source_definition->description }}
                </p>
            </li>
            <li class="list-group-item">
                <h5 class="list-group-item-heading">Source Type</h5>
                <p class="list-group-item-text">
                    {{ $source_definition->getType() }}
                </p>
            </li>
        </ul>
    </div>

@stop