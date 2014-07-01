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
            <a class="navbar-brand admin" href="{{ URL::to('api/admin') }} ">
                <img src='{{ URL::to("img/logo.png") }}' alt='Datatank logo' />
                <h1>Admin</h1>
            </a>

            <ul class="nav navbar-nav">
                @foreach($menu as $item)
                     @if(empty($item['permission']) || Tdt\Core\Auth\Auth::hasAccess($item['permission']))
                        <li @if(Request::segment(3) == $item['slug'])  class='active' @endif>
                            <a href="{{ URL::to('api/admin/' . $item['slug']) }}">
                                <i class='fa {{ $item['icon'] }}'></i> {{ $item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>

            @yield('navigation')

            @if(\Request::header('Authorization', null) === null)
                <ul class='pull-right nav navbar-nav'>
                    <li>
                        <a href="{{ URL::to('api/admin/logout') }}">
                            <i class='fa fa-sign-out'></i> Sign out
                        </a>
                    </li>
                </ul>
            @endif
        </nav>

        <div class="wrapper">
            <div id='content' class='row'>
                @yield('content')
            </div>

            <div class='push'></div>
        </div>

        <footer>
            <div class="col-lg-12">
                Powered by <a href="//thedatatank.com/" target="_blank">The DataTank</a>
            </div>
        </footer>
        <script type='text/javascript'>
            var baseURL = '{{ URL::to('') }}/';
            var authHeader = '{{ Request::header('Authorization') }}';
        </script>
        <script src="{{ URL::to("js/script.min.js") }}" type="text/javascript"></script>
        <script src="{{ URL::to("js/admin.min.js") }}" type="text/javascript"></script>
    </body>
</html>