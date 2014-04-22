@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <h3>Collections &amp; datasets</h3>
        <div class="input-group">
            <input id='dataset-filter' type="text" class="form-control" placeholder='Search for collection(s) or dataset(s)'>
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" disabled>Filter</button>
            </span>
        </div>

        <br/>

        @foreach($body->collections as $collection)
            <?php
                $collection_name = str_replace(URL::to('/'). '/', '', $collection);
            ?>

            <div class="panel dataset dataset-link collection panel-default clickable-row" data-href='{{ URL::to($collection) }}'>
                <div class="panel-body">
                    <div class='icon'>
                        <i class='fa fa-lg fa-folder-open-o'></i>
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-5'>
                                <h4 class='dataset-title'>
                                    <a href='{{ URL::to($collection) }}'>{{ $collection_name }}</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

        @foreach($body->datasets as $definition)
            <?php
                $definition_name = str_replace(URL::to('/'). '/', '', $definition);
            ?>

            <div class="panel dataset dataset-link panel-default clickable-row" data-href='{{ URL::to($definition) }}'>
                <div class="panel-body">
                    <div class='icon'>
                        <i class='fa fa-lg fa-file-text-o'></i>
                    </div>
                    <div>
                        <div class='row'>
                            <div class='col-sm-5'>
                                <h4 class='dataset-title'>
                                    <a href='{{ URL::to($definition) }}'>{{ $definition_name }}</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

        <div class='empty'>
            <div class='panel panel-default hide'>
                <div class="panel-body note">
                    <i class='fa fa-lg fa-warning'></i>&nbsp;&nbsp;
                    No collection(s) or dataset(s) found with the filter <strong>'<span class='dataset-filter'></span>'</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <ul class="list-group">
            <li class="list-group-item no-padding">
                <a href="{{ $dataset_link }}.json{{ $query_string }}" class="btn btn-block btn-primary"><i class='fa fa-file-text-o'></i> View as JSON</a>
            </li>
            <li class="list-group-item">
                <h5 class="list-group-item-heading">Collection</h5>
                <p class="list-group-item-text">
                    This URI is a collection, and can contain datasets and other collections.
                </p>
            </li>
        </ul>
    </div>

@stop
