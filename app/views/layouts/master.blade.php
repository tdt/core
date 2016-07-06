<!DOCTYPE html>
<html lang='en'>
    <head profile="http://dublincore.org/documents/dcq-html/">
        <title>{{ $title }}</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="DC.title" content="{{ $title }}"/>

        <link href='//fonts.googleapis.com/css?family=Varela+Round|Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <link rel='stylesheet' href='{{ asset("css/main.css", Config::get('app.ssl_enabled')) }}' type='text/css'/>
    </head>

    <body>
        <nav class="navbar navbar-fixed-top">
            <a class="navbar-brand" href="{{ URL::to('') }}">
                <img src='{{ asset("img/logo.png", Config::get('app.ssl_enabled')) }}' alt='Datatank logo' />
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
        <script src="{{ asset("js/script.min.js", Config::get('app.ssl_enabled')) }}" type="text/javascript"></script>
        @if (!empty($json_ld))
        <script type="application/ld+json">
            {{ $json_ld }}
        </script>
        @endif
         @if ( Config::get('app.debug') )
        <script type="text/javascript">
            document.write('<script src="//localhost:35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
        </script>
        @endif
    </body>
</html>
