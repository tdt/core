@extends('layouts.admin')

@section('content')

    <form class="form-horizontal" role="form">
        <div class='row'>
            <div class="col-sm-7">
                <h3>
                    <a href='{{ URL::to('api/admin/datasets') }}' class='back'>
                        <i class='fa fa-angle-left'></i> Back
                    </a>
                    Edit a dataset
                </h3>
            </div>
            <div class="col-sm-5 text-right">
                <button type='submit' class='btn btn-primary pull-right margin-left'><i class='fa fa-save'></i> Save</button>
            </div>
        </div>

        <div class="col-sm-6">

            @if(!empty($parameters_required))
                <hr/>

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4>Required parameters</h4>
                    </div>
                </div>

                @foreach($parameters_required as $parameter => $object)
                    <div class="form-group">
                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                            {{ str_replace('_', ' ', ucfirst($parameter)) }}
                        </label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $source_definition->{$parameter} }}'>
                            <div class='help-block'>
                                {{ $object->description }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif


            @if(!empty($parameters_optional))
                <hr/>

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4>Optional parameters</h4>
                    </div>
                </div>

                @foreach($parameters_optional as $parameter => $object)
                    <div class="form-group">
                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                            {{ str_replace('_', ' ', ucfirst($parameter)) }}
                        </label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $source_definition->{$parameter} }}'>
                            <div class='help-block'>
                                {{ $object->description }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="col-sm-6">

            <hr/>

            @if(!empty($parameters_dc))

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4>Dublin Core</h4>
                    </div>
                </div>

                @foreach($parameters_dc as $parameter => $object)
                    <div class="form-group">
                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                            {{ str_replace('_', ' ', ucfirst($parameter)) }}
                        </label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $definition->{$parameter} }}'>
                            <div class='help-block'>
                                {{ $object->description }}
                            </div>
                        </div>
                    </div>
                @endforeach

            @endif

            <button type='submit' class='btn btn-primary pull-right margin-left'><i class='fa fa-save'></i> Save</button>
            <br/>
            <br/>
            <br/>
        </div>


    </form>
@stop