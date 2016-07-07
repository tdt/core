@extends('layouts.master')

@section('content')

    <div class="col-sm-12">

        @foreach($definitions as $definition)

            <a class="panel dataset panel-default clickable-row" href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}'>
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
                            <div class='col-md-5'>
                                <h4 class='dataset-title'>
                                    <a href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}'>{{ $definition->collection_uri . '/' . $definition->resource_name }}</a>
                                </h4>
                                <div class='note dataset-description'>
                                    {{ $definition->source()->first()->description }}
                                </div>
                            </div>
                            <div class='col-md-7 text-right hidden-sm hidden-xs'>
                                <span class='note'>
                                    {{ $definition->rights }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

        @endforeach

    </div>

    <div class='col-sm-12 empty'>
        <div class='panel panel-default @if(count($definitions) > 0) hide @endif'>
            <div class="panel-body note">
                <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                @if(count($definitions) == 0)
                    {{ trans('datasets.no_datasets_message') }}
                @else
                    {{ trans('datasets.no_datasets_filter_message') }}<strong>'<span class='dataset-filter'></span>'</strong>
                @endif
            </div>
        </div>
    </div>

@stop

@section('navigation')

    <div class="search pull-right hidden-xs">
        <input id='dataset-filter' type="text" placeholder="{{ trans('datasets.search_datasets') }}" spellcheck='false'>
        <i class='fa fa-search'></i>
    </div>

    <?php $tracker_id = \Config::get('tracker.id'); ?>

    @if(!empty($tracker_id))
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', "{{ $tracker_id }}", 'auto');
          ga('send', 'pageview');

        </script>
    @endif
@stop
