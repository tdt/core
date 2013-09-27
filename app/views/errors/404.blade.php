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
        <div class="container wrapper">
            <div class="error clearfix">
                <div class='logo'>
                    <img src='{{ URL::to('img/logo.png') }}'/>
                </div>

                <h1>404</h1>
                <h3>Oops, there is nothing here!</h1>
                <p>{{ $exception->getMessage() }}</p>
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