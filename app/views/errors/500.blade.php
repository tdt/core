<!DOCTYPE html>
<html>

<head>
    <title>Error</title>
    <style type="text/css">
    html,
    body {
        height: 100%;
    }

    body {
        margin: 0;
        padding: 0;
        color: #B0BEC5;
        font-family: lato, open sans, helvetica neue, helvetica, segoe ui, sans-serif;
    }

    .container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    h1 {
        font-size: 72px;
        font-weight: 100;
        margin-bottom: 1em
    }

    p {
        margin-top: 3em;
    }

    a {
        color: #390;
    }

    .return {
        display: inline-block;
        padding: 1em 2em;
        border: 2px solid #390;
        font-size: 24px;
        text-decoration: none;
        text-transform: uppercase;
        font-weight: 300;
        letter-spacing: 2px;
    }

    .return:hover {
        outline: 2px solid #390;
    }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <h1>Error 500</h1>
            <p>
                <a href="/" class="return">Return to homepage</a>
            </p>
            <div>
                <p>The error message is: {{ $exception->getMessage() }}</p>
            </div>
            <p>
                If this was unexpected, please let us know: <a href="mailto:info@thedatatank.com">info@thedatatank.com</a>
            </p>
        </main>
    </div>
</body>

</html>
