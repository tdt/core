<!DOCTYPE html>
<html lang='en'>
    <head profile="http://dublincore.org/documents/dcq-html/">
        <title>{{ $title }}</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="DC.title" content="{{ $title }}"/>

        <link rel='stylesheet' href='{{ URL::to("css/main.css") }}' type='text/css'/>
    </head>

    <body>
        <nav class="navbar navbar-fixed-top">
            <a class="navbar-brand" href="{{ URL::to('') }} ">
                <img src='{{ URL::to("img/logo.png") }}' alt='Datatank logo' />
                <h1>Admin</h1>
            </a>

            <ul class="nav navbar-nav navbar-right">
                @if(tdt\core\auth\Auth::hasAccess('admin.dataset.view'))
                    <li @if(Request::segment(3) == '' || Request::segment(3) == 'datasets')  class='active' @endif>
                        <a href="{{ URL::to('api/admin/datasets') }}">
                            <i class='fa fa-table'></i> Datasets
                        </a>
                    </li>
                @endif
                @if(tdt\core\auth\Auth::hasAccess('admin.user.view'))
                    <li @if(Request::segment(3) == 'users')  class='active' @endif>
                        <a href="{{ URL::to('api/admin/users') }}">
                            <i class='fa fa-user'></i> Users
                        </a>
                    </li>
                @endif
                @if(tdt\core\auth\Auth::hasAccess('admin.group.view'))
                    <li @if(Request::segment(3) == 'groups')  class='active' @endif>
                        <a href="{{ URL::to('api/admin/groups') }}">
                            <i class='fa fa-group'></i> Groups
                        </a>
                    </li>
                @endif
            </ul>
        </nav>

        <div class="wrapper">
            <div id='content' class='row'>
                @yield('content')
            </div>

            <div class='push'></div>
        </div>

        <footer>
            <div class="col-lg-12">
                The DataTank &middot; <a href="//thedatatank.com/" target="_blank">Visit our website</a>
            </div>
        </footer>
        <script src="{{ URL::to("js/script.min.js") }}" type="text/javascript"></script>
        <script src="{{ URL::to("js/admin.min.js") }}" type="text/javascript"></script>
    </body>
</html>