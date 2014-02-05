@extends('layouts.admin')

@section('content')

    <div class='row header'>
        <div class="col-sm-3">
            <h3>
                <a href='{{ URL::to('api/admin/datasets') }}' class='back'>
                    <i class='fa fa-angle-left'></i>
                </a>
                Add a dataset
            </h3>
        </div>
        <div class='col-sm-8'>
            <ul class="nav nav-tabs">
                @foreach($mediatypes as $mediatype => $type)
                    <li @if($mediatype == 'csv') class='active' @endif><a href="#{{ $mediatype }}" data-toggle="tab">{{ strtoupper($mediatype) }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class='col-sm-1 text-right'>
            <button type='submit' class='btn btn-cta btn-add-dataset margin-left'><i class='fa fa-plus'></i> Add</button>
        </div>
    </div>

    <br/>
    <div class="tab-content">
        @foreach($mediatypes as $mediatype => $type)
            <div class="tab-pane @if($mediatype == 'csv') active @endif" id="{{ $mediatype }}"
                    data-mediatype='{{ $mediatype }}'>

                <div class='row'>
                    <div class="col-sm-12">
                        <div class="alert alert-danger error hide">
                            <i class='fa fa-2x fa-exclamation-circle'></i> <span class='text'></span>
                        </div>
                    </div>
                </div>

                <form class="form-horizontal add-dataset" role="form">

                    <div class="col-sm-6 panel panel-default dataset-parameters">

                        @if(!empty($type['parameters_required']))

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                </label>
                                <div class="col-sm-10">
                                    <h4>Required parameters</h4>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="input_identifier" class="col-sm-2 control-label">
                                    Identifier
                                </label>
                                <div class="col-sm-10">

                                    <div class="input-group">
                                        <span class="input-group-addon">{{ URL::to('') }}/</span>
                                        <input type="text" class="form-control" id="input_identifier" name="collection" placeholder="">
                                    </div>

                                    <div class='help-block'>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="input_identifier" class="col-sm-2 control-label">
                                    Type
                                </label>
                                <div class="col-sm-10">

                                    <input type="text" class="form-control" id="input_type" name="type" placeholder="" disabled value='{{ $mediatype }}'/>

                                    <div class='help-block'>
                                    </div>
                                </div>
                            </div>


                            @foreach($type['parameters_required'] as $parameter => $object)
                                <div class="form-group">
                                    <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                                        {{ $object->name }}
                                    </label>
                                    <div class="col-sm-10">
                                        @if($object->type == 'string')
                                            <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" @if(isset($object->default_value)) value='{{ $object->default_value }}' @endif>
                                        @elseif($object->type == 'text')
                                            <textarea class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}"> @if(isset($object->default_value)){{ $object->default_value }}@endif</textarea>
                                        @elseif($object->type == 'integer')
                                            <input type="number" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" @if(isset($object->default_value)) value='{{ $object->default_value }}' @endif>
                                        @elseif($object->type == 'boolean')
                                            <input type='checkbox' class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" checked='checked'/>
                                        @endif
                                        <div class='help-block'>
                                            {{ $object->description }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif


                        @if(!empty($type['parameters_optional']))
                            <hr/>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                </label>
                                <div class="col-sm-10">
                                    <h4>Optional parameters</h4>
                                </div>
                            </div>

                            @foreach($type['parameters_optional'] as $parameter => $object)
                                <div class="form-group">
                                    <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                                        {{ $object->name }}
                                    </label>
                                    <div class="col-sm-10">
                                        @if($object->type == 'string')
                                            <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" @if(isset($object->default_value)) value='{{ $object->default_value }}' @endif>
                                        @elseif($object->type == 'text')
                                            <textarea class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}"> @if(isset($object->default_value)){{ $object->default_value }}@endif</textarea>
                                        @elseif($object->type == 'integer')
                                            <input type="number" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" @if(isset($object->default_value)) value='{{ $object->default_value }}' @endif>
                                        @elseif($object->type == 'boolean')
                                            <input type='checkbox' class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" checked='checked'/>
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

                        @if(!empty($type['parameters_dc']))

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                </label>
                                <div class="col-sm-10">
                                    <h4><i class='fa fa-info-circle'></i> Describe your data</h4>
                                </div>
                            </div>

                            @foreach($type['parameters_dc'] as $parameter => $object)
                                <div class="form-group">
                                    <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                                        {{ $object->name }}
                                    </label>
                                    <div class="col-sm-10">
                                        @if($object->type == 'string')
                                            <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="">
                                        @elseif($object->type == 'list')
                                            <select id="input_{{ $parameter }}" name="{{ $parameter }}">
                                                <option></option>
                                                @foreach($object->list as $option)
                                                    <option>{{ $option }}</option>
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
            </div>
        @endforeach
    </div>
@stop