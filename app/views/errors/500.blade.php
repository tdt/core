<!DOCTYPE html>
<html lang='en'>
    <head profile="http://dublincore.org/documents/dcq-html/">
        <title>The DataTank</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href='{{ URL::to('css/error.css') }}' rel='stylesheet' type='text/css'/>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
    </head>

    <body>
        <div class="wrapper">
            <div class="error col-sm-8 col-sm-offset-2">
                <div class='logo col-md-3 hidden-sm hidden-xs'>
                    <img src='{{ URL::to('img/logo.png') }}'/>
                </div>
                <div class='col-md-9'>
                    <h1>500</h1>
                    <h3>Oops, we did something wrong!</h1>
                    @if(App::environment() == 'local' || App::environment() == 'testing')
                        <div class='row'>
                            <div class='col-lg-2'>
                                <strong>Message</strong>
                            </div>
                            <div class='col-lg-10'>
                                {{ $exception->getMessage() }}
                            </div>
                            <div class='col-lg-2'>
                                <strong>Class</strong>
                            </div>
                            <div class='col-lg-10'>
                                {{ $exception->getFile() }}
                            </div>
                            <div class='col-lg-2'>
                                <strong>On Line</strong>
                            </div>
                            <div class='col-lg-10'>
                                <span class='badge'>{{ $exception->getLine() }}</span>
                            </div>
                            <div class='col-lg-2'>
                                <strong>Trace</strong>
                            </div>
                            <div class='col-lg-10'>
                                {{ $exception->getTraceAsString() }}
                            </div>
                        </div>
                    @else
                        <p>If this error persists, get in touch with us!</p>
                    @endif
                </div>
            </div>

            <div class='push'></div>
        </div>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <a href="http://thedatatank.com/" target="_blank">The DataTank</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>