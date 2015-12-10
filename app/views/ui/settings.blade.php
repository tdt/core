@extends('layouts.admin')

@section('content')

    <form class="form-horizontal edit-dataset" role="form" method="post" action="">
        <div class='row header'>
            <div class="col-sm-7">
                <h3>{{ trans('settings.header') }}</h3>
            </div>
            <div class="col-sm-5 text-right">
                <button type='submit' class='btn btn-cta margin-left'><i class='fa fa-save'></i> {{ trans('settings.save') }}</button>
            </div>
        </div>

        <div class="col-sm-12">

            <br/>

            @if($error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endif
        </div>

        <div class="col-md-6 col-md-offset-3 panel panel-default dataset-parameters">

            <div class="form-group">
                <label for="input_" class="col-sm-2 control-label">
                    {{ trans('settings.title') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="catalog_title" placeholder=""  value="{{ $settings['catalog_title'] }}"/>
                    <div class='help-block'>
                        {{ trans('settings.title_help') }}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="input_" class="col-sm-2 control-label">
                    {{ trans('settings.description') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="catalog_description" placeholder="" value="{{ $settings['catalog_description'] }}"/>
                    <div class='help-block'>
                        {{ trans('settings.description_help') }}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="input_" class="col-sm-2 control-label">
                    {{ trans('settings.publisher') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="catalog_publisher_name" placeholder=""  value="{{ $settings['catalog_publisher_name'] }}"/>
                    <div class='help-block'>
                        {{ trans('settings.publisher_help') }}
                    </div>
                    <input type="text" class="form-control" name="catalog_publisher_uri" placeholder="" value="{{ $settings['catalog_publisher_uri'] }}" />
                    <div class='help-block'>
                        {{ trans('settings.publisher_uri_help') }}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="input_" class="col-sm-2 control-label">
                    {{ trans('settings.language') }}
                </label>
                <div class="col-sm-10">
                    <select name="catalog_language">
                        @foreach ($languages as $language)
                        <option @if ($language->lang_code === $settings['catalog_language']) {{ 'selected="selected"' }}@endif value='{{ $language->lang_code }}'>{{ $language->name }}</option>
                        @endforeach
                    </select>
                    <div class='help-block'>
                        {{ trans('settings.language_help') }}
                    </div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class="col-md-6 col-md-offset-3 text-right">
                <a href='{{ URL::to("api/dcat") }}'>{{ trans('settings.dcat_link') }} <i class='fa fa-link'></i></a>
            </div>
        </div>

    </form>
@stop