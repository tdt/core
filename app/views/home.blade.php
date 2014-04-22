@extends('layouts.master')

@section('content')

    <div class="col-sm-12">

        @foreach($definitions as $definition)

            <div class="panel dataset panel-default clickable-row" data-href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}'>
                <div class="panel-body">
                    <div class='icon'>
                        @if($definition->source_type == 'CsvDefinition' or $definition->source_type == 'XlsDefinition')
                            <i class='fa fa-lg fa-table'></i>
                        @elseif($definition->source_type == 'LdDefinition' or $definition->source_type == 'SparqlDefinition')
                            <i class='fa fa-lg fa-code-fork'></i>
                        @elseif($definition->source_type == 'ShpDefinition')
                            <i class='fa fa-lg fa-map-marker'></i>
                        @elseif($definition->source_type == 'XmlDefinition')
                            <i class='fa fa-lg fa-code'></i>
                        @else
                            <i class='fa fa-lg fa-file-text-o'></i>
                        @endif
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-5'>
                                <h4 class='dataset-title'>
                                    <a href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}'>{{ $definition->collection_uri . '/' . $definition->resource_name }}</a>
                                </h4>
                            </div>
                            <div class='col-sm-7 text-right hidden-sm hidden-xs'>
                                <span class='note'>{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}</span>
                            </div>
                        </div>
                        <div class='note dataset-description'>
                            {{ $definition->source()->first()->description }}
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

    </div>

    <div class='col-sm-12 empty'>
        <div class='panel panel-default @if(count($definitions) > 0) hide @endif'>
            <div class="panel-body note">
                <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                @if(count($definitions) == 0)
                    This datatank is hungry for data, no datasets were added yet.
                @else
                    No dataset(s) found with the filter <strong>'<span class='dataset-filter'></span>'</strong>
                @endif
            </div>
        </div>
    </div>

@stop

@section('navigation')

    <div class="search pull-right hidden-xs">
        <input id='dataset-filter' type="text" placeholder='Search for datasets' spellcheck='false'>
        <i class='fa fa-search'></i>
    </div>

@stop