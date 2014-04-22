@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <pre class="prettyprint linenums:1 lang-js ">{{{ $body }}}</pre>
    </div>

    <div class="col-sm-3">
        @include('dataset/partials/details')
    </div>
@stop

@section('navigation')
    @include('dataset/partials/pagination')
@stop