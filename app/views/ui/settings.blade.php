@extends('layouts.admin')

@section('content')

    <form class="form-horizontal edit-dataset" role="form" method="post" action="">
        <div class='row header'>
            <div class="col-sm-7">
                <h3>General settings</h3>
            </div>
            <div class="col-sm-5 text-right">
                <button type='submit' class='btn btn-cta margin-left'><i class='fa fa-save'></i> Save</button>
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
                    Title
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="catalog_title" placeholder=""  value="{{ $settings['catalog_title'] }}"/>
                    <div class='help-block'>
                        The name given to the catalog of datasets
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="input_" class="col-sm-2 control-label">
                    Description
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="catalog_description" placeholder="" value="{{ $settings['catalog_description'] }}"/>
                    <div class='help-block'>
                        Description of the general theme of the datasets
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="input_" class="col-sm-2 control-label">
                    Publisher
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="catalog_publisher_name" placeholder=""  value="{{ $settings['catalog_publisher_name'] }}"/>
                    <div class='help-block'>
                        The name of the entity responsible for publishing the catalog
                    </div>
                    <input type="text" class="form-control" name="catalog_publisher_uri" placeholder="" value="{{ $settings['catalog_publisher_uri'] }}" />
                    <div class='help-block'>
                        <strong>The URI</strong> of the entity responsible for publishing the catalog
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="input_" class="col-sm-2 control-label">
                    Language
                </label>
                <div class="col-sm-10">
                    <select name="catalog_language">
                        @foreach ($languages as $language)
                        <option @if ($language->lang_code === $settings['catalog_language']) {{ 'selected="selected"' }}@endif value='{{ $language->lang_code }}'>{{ $language->name }}</option>
                        @endforeach
                    </select>
                    <div class='help-block'>
                        The language of the majority of datasets
                    </div>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class="col-md-6 col-md-offset-3 text-right">
                <a href='{{ URL::to("api/dcat") }}'>DCAT-AP feed <i class='fa fa-link'></i></a>
            </div>
        </div>

    </form>


@stop