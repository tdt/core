@extends('layouts.admin')

@section('content')

    <div class='row header'>
        <div class="col-sm-7">
            <h3>{{ trans('admin.header_dataset_panel') }}</h3>
        </div>
        <div class="col-sm-5 text-right">
            <a href='{{ URL::to('api/admin/datasets/add') }}' class='btn btn-primary margin-left'
                data-step='1'
                data-intro='Add a new dataset to the system.'
                data-position="left">
                <i class='fa fa-plus'></i> {{ trans('admin.add_button') }}
            </a>
        </div>
    </div>

    <div class="col-sm-12">

        <h4>Created datasets</h4>
        @if(empty($definitions) || $definitions->count() == 0)
        <div class='panel panel-default'>
            <div class="panel-body note">
            <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                You have not created any datasets yet.
            </div>
        </div>
        @else
        <?php $i = 0; ?>
        @foreach($definitions as $definition)

            <div class="panel dataset dataset-link button-row panel-default  @if(Tdt\Core\Auth\Auth::hasAccess('admin.dataset.update')) clickable-row @endif" data-href='{{ URL::to('api/admin/datasets/edit/' . $definition->id) }}'>
                <div class="panel-body"
                    @if($i==0)
                        data-step='3'
                        data-intro='This is one of your definitions, click this row to start <strong>editing</strong> it.'
                        data-position="bottom"
                    @endif
                    >
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
                            @if($definition->draft_flag==1)  - draft, indexing in progress @endif
                                </h4>
                            </div>
                            <div class='col-sm-4 text-right'>
                                <div class='btn-group'>
                                    @if(Tdt\Core\Auth\Auth::hasAccess('dataset.view'))
                                        <a href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the dataset'
                                            @if($i==0)
                                                data-step='4'
                                                data-intro='<strong>View</strong> your dataset.'
                                                data-position="left"
                                            @endif
                                            >
                                            <i class='fa fa-eye'></i> {{ trans('admin.view_data') }}
                                        </a>
                                    @endif
                                    @if(Tdt\Core\Auth\Auth::hasAccess('definition.view'))
                                        <a href='{{ URL::to('api/definitions/'. $definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the JSON definition'

                                            @if($i==0)
                                                data-step='5'
                                                data-intro='Link to the <strong>JSON document</strong> that consists of all the parameters the definition has.'
                                                data-position="left"
                                            @endif
                                            >
                                            <i class='fa fa-external-link'></i> {{ trans('admin.view_definition') }}
                                        </a>
                                    @endif
                                    @if(Tdt\Core\Auth\Auth::hasAccess('admin.dataset.delete'))
                                        <a href='{{ URL::to('api/admin/datasets/delete/'. $definition->id) }}' class='btn delete' title='Delete this dataset'

                                           @if($i==0)
                                               data-step='6'
                                               data-intro='Removes the <strong>entire definition</strong>, and the identifier that was used will become available again.'
                                               data-position="left"
                                           @endif
                                            >
                                            <i class='fa fa-times icon-only'></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $i++; ?>
        @endforeach
        @endif
    </div>
    <div class="col-sm-12">
        <h4>Updated datasets</h4>
        @if(empty($definitions_updated) || $definitions_updated->count() == 0)
        <div class='panel panel-default'>
            <div class="panel-body note">
            <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                You have not updated any datasets yet.
            </div>
        </div>
        @else
        <?php $i = 0; ?>
        @foreach($definitions_updated as $definition)

            <div class="panel dataset dataset-link button-row panel-default  @if(Tdt\Core\Auth\Auth::hasAccess('admin.dataset.update')) clickable-row @endif" data-href='{{ URL::to('api/admin/datasets/edit/' . $definition->id) }}'>
                <div class="panel-body"
                    @if($i==0)
                        data-step='3'
                        data-intro='This is one of your definitions, click this row to start <strong>editing</strong> it.'
                        data-position="bottom"
                    @endif
                    >
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
                            @if($definition->draft_flag==1)  - draft, indexing in progress @ @endif
                                </h4>
                            </div>
                            <div class='col-sm-4 text-right'>
                                <div class='btn-group'>
                                    @if(Tdt\Core\Auth\Auth::hasAccess('dataset.view'))
                                        <a href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the dataset'
                                            @if($i==0)
                                                data-step='4'
                                                data-intro='<strong>View</strong> your dataset.'
                                                data-position="left"
                                            @endif
                                            >
                                            <i class='fa fa-eye'></i> {{ trans('admin.view_data') }}
                                        </a>
                                    @endif
                                    @if(Tdt\Core\Auth\Auth::hasAccess('definition.view'))
                                        <a href='{{ URL::to('api/definitions/'. $definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the JSON definition'

                                            @if($i==0)
                                                data-step='5'
                                                data-intro='Link to the <strong>JSON document</strong> that consists of all the parameters the definition has.'
                                                data-position="left"
                                            @endif
                                            >
                                            <i class='fa fa-external-link'></i> {{ trans('admin.view_definition') }}
                                        </a>
                                    @endif
                                    @if(Tdt\Core\Auth\Auth::hasAccess('admin.dataset.delete'))
                                        <a href='{{ URL::to('api/admin/datasets/delete/'. $definition->id) }}' class='btn delete' title='Delete this dataset'

                                           @if($i==0)
                                               data-step='6'
                                               data-intro='Removes the <strong>entire definition</strong>, and the identifier that was used will become available again.'
                                               data-position="left"
                                           @endif
                                            >
                                            <i class='fa fa-times icon-only'></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $i++; ?>
        @endforeach
        @endif
    </div>
    <div class="col-sm-12">
        <h4>Other datasets</h4>
        @if(empty($definitions_others) || $definitions_others->count() == 0)
        <div class='panel panel-default'>
            <div class="panel-body note">
            <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                This datatank is hungry for data, no other datasets were added yet.
            </div>
        </div>
        @else
        <?php $i = 0; ?>
        @foreach($definitions_others as $definition)

            <div class="panel dataset dataset-link button-row panel-default  @if(Tdt\Core\Auth\Auth::hasAccess('admin.dataset.update')) clickable-row @endif" data-href='{{ URL::to('api/admin/datasets/edit/' . $definition->id) }}'>
                <div class="panel-body"
                    @if($i==0)
                        data-step='3'
                        data-intro='This is one of your definitions, click this row to start <strong>editing</strong> it.'
                        data-position="bottom"
                    @endif
                    >
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
                            @if($definition->draft_flag==1)  - draft, indexing in progress @ @endif
                                </h4>
                            </div>
                            <div class='col-sm-4 text-right'>
                                <div class='btn-group'>
                                    @if(Tdt\Core\Auth\Auth::hasAccess('dataset.view'))
                                        <a href='{{ URL::to($definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the dataset'
                                            @if($i==0)
                                                data-step='4'
                                                data-intro='<strong>View</strong> your dataset.'
                                                data-position="left"
                                            @endif
                                            >
                                            <i class='fa fa-eye'></i> {{ trans('admin.view_data') }}
                                        </a>
                                    @endif
                                    @if(Tdt\Core\Auth\Auth::hasAccess('definition.view'))
                                        <a href='{{ URL::to('api/definitions/'. $definition->collection_uri . '/' . $definition->resource_name) }}' class='btn' title='View the JSON definition'

                                            @if($i==0)
                                                data-step='5'
                                                data-intro='Link to the <strong>JSON document</strong> that consists of all the parameters the definition has.'
                                                data-position="left"
                                            @endif
                                            >
                                            <i class='fa fa-external-link'></i> {{ trans('admin.view_definition') }}
                                        </a>
                                    @endif
                                    @if(Tdt\Core\Auth\Auth::hasAccess('admin.dataset.delete'))
                                        <a href='{{ URL::to('api/admin/datasets/delete/'. $definition->id) }}' class='btn delete' title='Delete this dataset'

                                           @if($i==0)
                                               data-step='6'
                                               data-intro='Removes the <strong>entire definition</strong>, and the identifier that was used will become available again.'
                                               data-position="left"
                                           @endif
                                            >
                                            <i class='fa fa-times icon-only'></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $i++; ?>
        @endforeach
        @endif
    </div>

<div class="col-sm-12">
<a href='#' class='introjs pull-right'>
<i class='fa fa-lg fa-question-circle'></i>
</a>
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

@section('navigation')
@if(count($definitions) > 0)
<div class="search pull-right hidden-xs">
<input id='dataset-filter' type="text" placeholder='Search for datasets' spellcheck='false'>
<i class='fa fa-search'></i>
</div>
@endif
@stop