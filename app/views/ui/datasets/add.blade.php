@extends('layouts.admin')

@section('content')

    <div class='row header'>
        <div class="col-sm-6">
            <h3>
                <a href='{{ URL::to('api/admin/datasets') }}' class='back'>
                    <i class='fa fa-angle-left'></i>
                </a>
                {{ trans('admin.add_dataset') }}
            </h3>
        </div>
        <div class='col-sm-6 text-right'>
            <button type='submit' class='btn btn-cta btn-add-dataset margin-left'><i class='fa fa-plus'></i> {{ trans('admin.add_button') }}</button>
        </div>
    </div>

    <br/>
    <form class="form-horizontal add-dataset" role="form">
        <div class='identifier'>
            <div class='row'>
                <div class='col-sm-offset-2 col-sm-8'>
                    <h3>1. {{ trans('admin.build_identifier') }}</h3>
                    <div class="form-group">
                        <label for="input_identifier" class="col-sm-2 control-label">
                            {{ trans('admin.identifier') }}
                        </label>
                        <div class="col-sm-8">

                            <div class="input-group">
                                <span class="input-group-addon" id='input_identifier_display' data-url='{{ URL::to('') }}/'>{{ URL::to('') }}/</span>
                                <input type="hidden" class="form-control" id="input_identifier" name="collection" placeholder="" disabled="disabled" >
                            </div>
                            <div class='help-block'>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_identifier" class="col-sm-2 control-label">
                            {{ trans('admin.collection') }}
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="input_collection" placeholder="">
                            <div class='help-block'>{{ trans('admin.collection_help') }}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_identifier" class="col-sm-2 control-label">
                            {{ trans('admin.resource_name') }}
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="input_resource_name" placeholder="">
                            <div class='help-block'>{{ trans('admin.resource_name_help') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3>2. {{ trans('admin.select_type') }}</h3>

        <ul class="nav nav-tabs">
            @foreach($mediatypes as $mediatype => $type)
                <li @if($mediatype == 'csv') class='active' @endif><a href="#{{ $mediatype }}" data-toggle="tab">{{ strtoupper($mediatype) }}</a></li>
            @endforeach
        </ul>

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

                    <div class="col-sm-6 panel dataset-parameters">

                        @if(!empty($type['parameters_required']))

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                </label>
                                <div class="col-sm-10">
                                    <h4>{{ trans('admin.required_parameters') }}</h4>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="input_identifier" class="col-sm-2 control-label">
                                    {{ trans('admin.type') }}
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
                                        @if($object->type == 'string')
                                            <div class="col-sm-10">
                                            <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" @if(isset($object->default_value)) value='{{ $object->default_value }}' @endif>
                                        @elseif($object->type == 'text')
                                            <div class="col-sm-10">
                                            <textarea class="form-control" rows=10 id="input_{{ $parameter }}" name="{{ $parameter }}"> @if (isset($object->default_value)) {{ $object->default_value }}@endif</textarea>
                                        @elseif($object->type == 'integer')
                                            <div class="col-sm-10">
                                            <input type="number" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" @if(isset($object->default_value)) value='{{ $object->default_value }}' @endif>
                                        @elseif($object->type == 'date')
                                            <div class="col-sm-4">
                                            <input type="date" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}">
                                        @elseif($object->type == 'boolean')
                                        <div class="col-sm-10">
                                            <input type='checkbox' class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" @if(isset($object->default_value) && $object->default_value) checked='checked' @endif/>
                                        @elseif($object->type == 'list')
                                        <div class="col-sm-10">
                                            <select id="input_{{ $parameter }}" name="{{ $parameter }}" class="form-control">
                                                <option></option>
                                                @foreach($object->list as $option)
                                                    @if(@$object->default_value == $option)
                                                        <option selected>{{ $option }}</option>
                                                    @else
                                                        <option>{{ $option }}</option>
                                                    @endif
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


                        @if(!empty($type['parameters_optional']))
                            <hr/>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                </label>
                                <div class="col-sm-10">
                                    <h4>{{ trans('admin.optional_parameters') }}</h4>
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
                                            <textarea class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}"> @if (isset($object->default_value)) {{ $object->default_value }}@endif</textarea>
                                        @elseif($object->type == 'integer')
                                            <input type="number" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" @if(isset($object->default_value)) value='{{ $object->default_value }}' @endif>
                                        @elseif($object->type == 'date')
                                            <input type="date" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}">
                                        @elseif($object->type == 'boolean')
                                            <input type='checkbox' class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" checked='checked'/>
                                        @elseif($object->type == 'list')
                                            <select id="input_{{ $parameter }}" name="{{ $parameter }}" class="form-control">
                                                <option></option>
                                                @foreach($object->list as $option)
                                                    @if(@$object->default_value == $option)
                                                        <option selected>{{ $option }}</option>
                                                    @else
                                                        <option>{{ $option }}</option>
                                                    @endif
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


                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                            </label>
                            <div class="col-sm-10">
                                <h4><i class='fa fa-clock-o'></i> {{ trans('admin.caching') }}</h4>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="input_cache_minutes" class="col-sm-2 control-label">
                                {{ trans('admin.cache_for') }}
                            </label>
                            <div class="col-sm-10">
                                <div class="input-group input-medium">
                                    <input type="text" class="form-control" id="input_cache_minutes" name="cache_minutes" placeholder="" value="5">
                                    <span class="input-group-addon">{{ trans('admin.minute') }}</span>
                                </div>

                                <div class='help-block'>
                                    {{ trans('admin.cache_help') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 panel dataset-parameters">

                        @if(!empty($type['parameters_dc']))

                            <div class="form-group">
                                <label class="col-sm-2 control-label">
                                </label>
                                <div class="col-sm-10">
                                    <div class="profile-selector checkbox">
                                        <label class="profile">
                                            <input type="radio" name="profile_{{$mediatype}}" value="dcat" checked>
                                            DCAT-AP
                                        </label>
                                        <label class="profile">
                                            <input type="radio" name="profile_{{$mediatype}}" value="geodcat">
                                            GeoDCAT-AP
                                        </label>
                                    </div>
                                    <h4><i class='fa fa-info-circle'></i> {{ trans('admin.dcat_header') }}</h4>
                                </div>
                            </div>

                            @foreach($type['parameters_dc'] as $parameter => $object)
                                <div class="form-group {{ $object->requiredgeodcat }}">
                                    <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                                        {{ $object->name }}
                                    </label>

                                        @if($object->type == 'string')
                                            <div class="col-sm-10">
                                            <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="">
                                        @elseif($object->type == 'date')
                                            <div class="col-sm-10 col-lg-5">
                                            <input type="date" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}">
                                        @elseif($object->type == 'list')
                                            <div class="col-sm-10">
                                            <select id="input_{{ $parameter }}" name="{{ $parameter }}" class="form-control">
                                                <option></option>
                                                @foreach($object->list as $option)
                                                    @if(@$object->default_value == $option)
                                                        <option selected>{{ $option }}</option>
                                                    @else
                                                        <option>{{ $option }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        @endif
                                        <div class='help-block' requirement="{{ trans('parameters.' . $object->requiredgeodcat) }}">
                                            {{ $object->description }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if(!empty($type['parameters_geodcat']))
                            <div class="profile-geodcat" style="display:none">
                                <div class="form-group profile-geodcat">
                                    <label class="col-sm-2 control-label">
                                    </label>
                                    <div class="col-sm-10">
                                        <h4><i class='fa fa-map-marker'></i> {{ trans('admin.geodcat_header') }}</h4>
                                    </div>
                                </div>
                                @foreach($type['parameters_geodcat'] as $parameter => $object)
                                    <div class="form-group {{ $object->requiredgeodcat }}">
                                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                                            {{ $object->name }}
                                        </label>
                                        <div class="col-sm-10">
                                            @if($object->type == 'string')
                                                <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="">
                                            @elseif($object->type == 'geojson')
                                                <input type="hidden" id="input_{{ $parameter }}" name="{{ $parameter }}">
                                                <div class="btn btn-default location-picker" data-id="input_{{ $parameter }}">Use location picker</div>
                                            @endif
                                            <div class='help-block' requirement="{{ trans('parameters.' . $object->requiredgeodcat) }}">
                                                {{ $object->description }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="form-group">
                                    <label for="input_attribution" class="col-sm-2 control-label">
                                        {{ trans('parameters.geodcat_attribution') }}
                                    </label>
                                    <div class="col-sm-10">
                                        <button type="button" class="btn btn-default btn-attribution">{{ trans('admin.add_button') }}</button>
                                        <select id="input_attribution" class="form-control select-attribution">
                                            @foreach(['author', 'maintainer'] as $role)
                                                <option value='{{ json_encode([
                                                'option' => $role,
                                                'name' => trans('parameters.role_' . $role),
                                                'desc' => trans('parameters.role_' . $role . '_desc'),
                                            ]) }}'>{{ trans('parameters.role_' . $role) }}</option>
                                            @endforeach
                                        </select>
                                        <div class='help-block'>
                                            {{ trans('parameters.geodcat_attribution_desc') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </form>
    <script type="text/x-template" id="person">
        <div class="attribution-person" data-role="#OPTION#">
            <div class="form-group">
                <label class="col-sm-2 control-label"> </label>
                <div class="col-sm-10"><h4>#ROLE# &nbsp; <small>#DESC#</small></h4></div>
            </div>
            <div class="form-group">
                <label for="input_attribution" class="col-sm-2 control-label">
                    {{ trans('parameters.person_name') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control name" id="input_{{ $parameter }}">
                </div>
            </div>
            <div class="form-group">
                <label for="input_attribution" class="col-sm-2 control-label">
                    {{ trans('parameters.person_email') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control email" id="input_{{ $parameter }}">
                </div>
            </div>
        </div>
    </script>
@stop