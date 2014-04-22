<!DOCTYPE html>
<html lang='en'>
    <head profile="http://dublincore.org/documents/dcq-html/">
        <title>{{ $title }}</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="DC.title" content="{{ $title }}"/>

        <link href='//fonts.googleapis.com/css?family=Varela+Round|Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <link rel='stylesheet' href='{{ URL::to("css/main.css") }}?v={{ Config::get('app.version', 4) }}' type='text/css'/>
    </head>

    <body>
        <nav class="navbar navbar-fixed-top">
            <a class="navbar-brand" href="{{ URL::to('') }}">
                <img src='{{ URL::to("img/logo.png") }}' alt='Datatank logo' />
                <h1>@if(!empty($page_title)){{ $page_title }}@endif</h1>
            </a>

            @yield('navigation')
        </nav>

        <div class="wrapper">
            <div id='content' class='row content-wrapper'>
                @yield('content')
            </div>

            <div class='push'></div>
        </div>

        <footer>
            <div class="col-lg-12">
                Powered by <a href="//thedatatank.com/" target="_blank">The DataTank</a>
            </div>
        </footer>
        <script src="{{ URL::to("js/script.min.js") }}" type="text/javascript"></script>
    </body>
</html>