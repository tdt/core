@extends('layouts.admin')

@section('content')

    <div class='row'>
        <div class="col-sm-7">
            <h3>Manage your data</h3>
        </div>
        <div class="col-sm-5 text-right">
            <a href='{{ URL::to('api/admin/datasets/add') }}' class='btn btn-primary pull-right margin-left'><i class='fa fa-plus'></i> Add</a>

            <div class="input-group">
                <input id='dataset-filter' type="text" class="form-control" placeholder='Search for datasets'>
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" disabled>Filter</button>
                </span>
            </div>

        </div>
    </div>

    <div class="col-sm-12">

        <br/>

        @foreach($definitions as $definition)

            <div class="panel dataset dataset-link button-row panel-default  @if(tdt\core\auth\Auth::hasAccess('admin.dataset.update')) clickable-row @endif" data-href='{{ URL::to('api/admin/datasets/edit/' . $definition->id) }}'>
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
                            <div class='col-sm-8'>
                                <h4 class='dataset-title'>
                                    <a href='{{ URL::to('api/admin/datasets/edit/' . $definition->id) }}'>{{ $definition->collection_uri . '/' . $definition->resource_name }}</a>
                                </h4>
                            </div>
                            <div class='col-sm-4 text-right'>
                                @if(tdt\core\auth\Auth::hasAccess('dataset.view'))
                                    <a href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the dataset'><i class='fa fa-eye'></i> Data</a>
                                @endif
                                @if(tdt\core\auth\Auth::hasAccess('definition.view'))
                                    <a href='{{ URL::to('api/definitions/'. $definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the JSON definition'><i class='fa fa-external-link'></i> Definition</a>
                                @endif
                                @if(tdt\core\auth\Auth::hasAccess('admin.dataset.delete'))
                                    <a href='{{ URL::to('api/admin/datasets/delete/'. $definition->id) }}' class='btn delete' title='Delete this dataset'><i class='fa fa-times icon-only'></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

    </div>

    <div class='col-sm-12 empty'>
        <div class='panel panel-default @if(!empty($definitions)) hide @endif'>
            <div class="panel-body note">
                <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                @if(empty($definitions))
                    This datatank is hungry for data, no datasets were added yet.
                @else
                    No dataset(s) found with the filter <strong>'<span class='dataset-filter'></span>'</strong>
                @endif
            </div>
        </div>
    </div>

@stop