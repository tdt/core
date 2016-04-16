@extends('layouts.admin')

@section('content')

    <form class="form-horizontal edit-dataset" role="form" data-mediatype='{{ strtolower($source_definition->type) }}'
        data-identifier='{{ $definition->collection_uri . '/' . $definition->resource_name }}'>
        <div class='row header'>
            <div class="col-sm-7">
                <h3>
                    <a href='{{ URL::to('api/admin/datasets') }}' class='back'>
                        <i class='fa fa-angle-left'></i>
                    </a>
                    {{ trans('admin.edit_dataset') }}
                </h3>
            </div>
            <div class="col-sm-5 text-right">
                <button type='submit' class='btn btn-cta btn-edit-dataset margin-left'><i class='fa fa-save'></i> {{ trans('admin.save') }}</button>
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

        <div class='row'>
        <div class="col-sm-6 panel panel-default dataset-parameters">
            @if(!empty($parameters_optional))
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4>{{ trans('admin.parameters') }}</h4>
                    </div>
                </div>


                <div class="form-group">
                    <label for="input_identifier" class="col-sm-2 control-label">
                        {{ trans('admin.source_type') }}
                    </label>
                    <div class="col-sm-10">
                        <label class="control-label">
                            {{ $source_definition->type }}
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="input_identifier" class="col-sm-2 control-label">
                        {{ trans('admin.identifier') }}
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
                                <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{{ $source_definition->{$parameter} }}}'>
                            @elseif($object->type == 'text')
                                <textarea class="form-control" rows=10 id="input_{{ $parameter }}" name="{{ $parameter }}">{{{ $source_definition->{$parameter} }}}</textarea>
                            @elseif($object->type == 'integer')
                                <input type="number" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $source_definition->{$parameter} }}'>
                            @elseif($object->type == 'date')
                                <input type="date" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="YYYY-MM-DD" value="{{ $source_definition->{$parameter} }}">
                            @elseif($object->type == 'boolean')
                                <input type='checkbox' class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" @if($source_definition->{$parameter}) checked='checked' @endif/>
                            @elseif($object->type == 'list')
                                <select id="input_{{ $parameter }}" name="{{ $parameter }}" class="form-control">
                                    <option></option>
                                    @foreach($object->list as $option)
                                        <option @if ($source_definition->{$parameter} == $option) {{ 'selected="selected"' }}@endif>{{ $option }}</option>
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
                        <input type="text" class="form-control" id="input_cache_minutes" name="cache_minutes" placeholder="" value="{{ $definition->cache_minutes }}">
                        <span class="input-group-addon">{{ trans('admin.minute') }}</span>
                    </div>

                    <div class='help-block'>
                        {{ trans('admin.cache_help') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 panel panel-default dataset-parameters panel-dcat">

            @if(!empty($parameters_dc))

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <div class="profile-selector checkbox">
                            <label class="profile">
                                <input type="radio" name="profile" value="dcat" {{ $definition->location? '' : 'checked' }}>
                                DCAT-AP
                            </label>
                            <label class="profile">
                                <input type="radio" name="profile" value="geodcat" {{ $definition->location? 'checked' : '' }}>
                                GeoDCAT-AP
                            </label>
                        </div>
                        <h4><i class='fa fa-info-circle'></i> {{ trans('admin.dcat_header') }} <small>DCAT</small></h4>
                    </div>
                </div>

                @foreach($parameters_dc as $parameter => $object)
                    <div class="form-group {{ $object->requiredgeodcat }}">
                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                            {{ $object->name }}
                        </label>
                        <div class="col-sm-10">
                            @if($object->type == 'string')
                                <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value='{{ $definition->{$parameter} }}'>
                            @elseif($object->type == 'date')
                                <input type="date" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="YYYY-MM-DD" value="{{ $definition->{$parameter} }}">
                            @elseif($object->type == 'list')
                                <select id="input_{{ $parameter }}" name="{{ $parameter }}" class="form-control">
                                    <option></option>
                                    @foreach($object->list as $option)
                                        <option @if ($definition->{$parameter} == $option) {{ 'selected="selected"' }}@endif>{{ $option }}</option>
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

            @if(!empty($parameters_geodcat))
            <div class="profile-geodcat" style="display:none">
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4><i class='fa fa-map-marker'></i> {{ trans('admin.geodcat_header') }} <small>GeoDCAT</small></h4>
                    </div>
                </div>
                @foreach($parameters_geodcat as $parameter => $object)
                    <div class="form-group {{ $object->requiredgeodcat }}">
                        <label for="input_{{ $parameter }}" class="col-sm-2 control-label">
                            {{ $object->name }}
                        </label>
                        <div class="col-sm-10">
                            @if($object->type == 'string')
                                <input type="text" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="" value="{{ $definition->location ? $definition->location->label->label : '' }}">
                            @elseif($object->type == 'date')
                                <input type="date" class="form-control" id="input_{{ $parameter }}" name="{{ $parameter }}" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d') }}">
                            @elseif($object->type == 'geojson')
                                <input type="hidden" id="input_{{ $parameter }}" name="{{ $parameter }}" value='{{ $definition->location ? json_encode($definition->location->geometry->geometry) : '' }}'>
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

                @foreach($definition->attributions as $key => $attribution)
                <div class="attribution-person" data-role="{{ $attribution->role }}">
                    <div class="form-group" style="margin-bottom: 0">
                        <label class="col-sm-2 control-label"> </label>
                        <div class="col-sm-10">
                            <h4>
                                <button class="btn btn-default pull-right btn-delete">{{ trans('admin.delete') }}</button>
                                {{ trans('parameters.role_' . $attribution->role) }} &nbsp; <small>{{ trans('parameters.role_' . $attribution->role . '_desc') }}</small>
                            </h4>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_attribution_name_{{ $key }}" class="col-sm-2 control-label">
                            {{ trans('parameters.person_name') }}
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control name" id="input_attribution_name_{{ $key }}" value="{{ $attribution->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_attribution_email_{{ $key }}" class="col-sm-2 control-label">
                            {{ trans('parameters.person_email') }}
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control email" id="input_attribution_email_{{ $key }}" value="{{ $attribution->email }}">
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
            @endif
        </div>
        </div>
    </form>
    <script type="text/x-template" id="person">
        <div class="attribution-person" data-role="#OPTION#">
            <div class="form-group" style="margin-bottom: 0">
                <label class="col-sm-2 control-label"> </label>
                <div class="col-sm-10">
                    <h4>
                        <button class="btn btn-default pull-right btn-delete">{{ trans('admin.delete') }}</button>
                        #ROLE# &nbsp; <small>#DESC#</small>
                    </h4>
                </div>
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