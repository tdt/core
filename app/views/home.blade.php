@extends('layouts.master')

@section('content')

    <dataset-list></dataset-list>
    <script src="{{ asset("js/datasets.min.js", Config::get('app.ssl_enabled')) }}" type="text/javascript"></script>

@stop

@section('navigation')
    <?php $tracker_id = \Config::get('tracker.id'); ?>

    @if(!empty($tracker_id))
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', "{{ $tracker_id }}", 'auto');
          ga('send', 'pageview');

        </script>
    @endif
@stop
