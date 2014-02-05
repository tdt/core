@extends('layouts.admin')

@section('content')

    <form class="form-horizontal edit-dataset" role="form" data-mediatype='{{ strtolower($source_definition->getType()) }}'
        data-identifier='{{ $definition->collection_uri . '/' . $definition->resource_name }}'>
        <div class='row header'>
            <div class="col-sm-7">
                <h3>
                    <a href='{{ URL::to('api/admin/datasets') }}' class='back'>
                        <i class='fa fa-angle-left'></i>
                    </a>
                    Edit a dataset
                </h3>
            </div>
            <div class="col-sm-5 text-right">
                <button type='submit' class='btn btn-cta btn-edit-dataset margin-left'><i class='fa fa-save'></i> Save</button>
            </div>
        </div>

        <br/>

        <div class='row'>
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissable error hide">
                    <i class='fa fa-2x fa-exclamation-circle'></i> <span class='text'></span>
                </div>
            </div>
        </div>

        <div class="col-sm-6 panel panel-default dataset-parameters">

            @if(!empty($parameters_optional))

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4>Parameters</h4>
                    </div>
                </div>


                <div class="form-group">
                    <label for="input_identifier" class="col-sm-2 control-label">
                        Source type
                    </label>
                    <div class="col-sm-10">
                        <label class="control-label">
                            {{ $source_definition->getType() }}
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="input_identifier" class="col-sm-2 control-label">
                        Identifier
                    </label>
                    <div class="col-sm-10">

                        <input type="text" class="form-control" id="input_identifier" placeholder="" value="{{  URL::to($definition->collection_uri . '/' . $definition->resource_name) }}" disabled>

                        <div class='help-block'>
                        </div>
                    </div>
                </div>

                @foreach($parameters_optional as $parameter => $object)
                    <div class="form-group">
                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                            {{ $object->name }}
                        </label>
                        <div class="col-sm-10">
                            @if($object->type == 'string')
                                <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $source_definition->{$parameter} }}'>
                            @elseif($object->type == 'text')
                                <textarea class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}">{{ $source_definition->{$parameter} }}</textarea>
                            @elseif($object->type == 'integer')
                                <input type="number" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $source_definition->{$parameter} }}'>
                            @elseif($object->type == 'boolean')
                                <input type='checkbox' class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" @if($source_definition->{$parameter}) checked='checked' @endif/>
                            @endif
                            <div class='help-block'>
                                {{ $object->description }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="col-sm-6">

            @if(!empty($parameters_dc))

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4><i class='fa fa-info-circle'></i> Describe your data</h4>
                    </div>
                </div>

                @foreach($parameters_dc as $parameter => $object)
                    <div class="form-group">
                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                            {{ $object->name }}
                        </label>
                        <div class="col-sm-10">
                            @if($object->type == 'string')
                                <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $definition->{$parameter} }}'>
                            @elseif($object->type == 'list')
                                <select id="input_{{ $parameter }}" name="{{ $parameter }}">
                                    @foreach($object->list as $option)
                                        <option @if( $definition->{$parameter} == $option){{ 'selected="selected"' }}@endif>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <div class='help-block'>
                                {{ $object->description }}
                            </div>
                        </div>
                    </div>
                @endforeach

            @endif
        </div>


    </form>
@stop