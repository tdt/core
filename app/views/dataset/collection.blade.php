@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <h3>{{ trans('datasets.list_header') }}</h3>
        <div class="input-group">
            <input id='dataset-filter' type="text" class="form-control" placeholder='Search for collection(s) or dataset(s)'>
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" disabled>{{ trans('datasets.filter') }}</button>
            </span>
        </div>

        <br/>

        @foreach($body->collections as $collection)
            <?php
                $collection_name = str_replace(URL::to('/'). '/', '', $collection);
            ?>

            <a class="panel dataset dataset-link collection panel-default clickable-row" href='{{ URL::to($collection) }}'>
                <div class="panel-body">
                    <div class='icon'>
                        <i class='fa fa-lg fa-folder-open-o'></i>
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-5'>
                                <h4 class='dataset-title'>
                                    <a href='{{ $collection }}'>{{ $collection_name }}</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

        @endforeach

        @foreach($body->datasets as $definition)
            <?php
                $definition_name = str_replace(URL::to('/'). '/', '', $definition);
            ?>

            <a class="panel dataset dataset-link panel-default clickable-row" href='{{ URL::to($definition) }}'>
                <div class="panel-body">
                    <div class='icon'>
                        <i class='fa fa-lg fa-file-text-o'></i>
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-5'>
                                <h4 class='dataset-title'>
                                    <a href='{{ $definition }}'>{{ $definition_name }}</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

        @endforeach

        <div class='empty'>
            <div class='panel panel-default hide'>
                <div class="panel-body note">
                    <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                    {{ trans('datasets.no_datasets_filter_message') }} <strong>'<span class='dataset-filter'></span>'</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <ul class="list-group">
            <li class="list-group-item no-padding">
                <a href="{{ $dataset_link }}.json{{ $query_string }}" class="btn btn-block btn-primary"><i class='fa fa-file-text-o'></i> {{ trans('datasets.view_json') }}</a>
            </li>
            <li class="list-group-item">
                <h5 class="list-group-item-heading">{{ trans('datasets.collection') }}</h5>
                <p class="list-group-item-text">
                    {{ trans('datasets.collection_description') }}
                </p>
            </li>
        </ul>
    </div>

@stop
