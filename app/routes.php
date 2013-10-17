<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/


/*
 * IMPORTANT!
 * The catch-all route to catch all other request is added last to allow packages to still have their own routes
 */
App::before(function(){
    // The (in)famous catch-all
    Route::any('{all}', 'tdt\core\BaseController@handleRequest')->where('all', '.*');
});

/*
 * Custom error pages
 */
App::error(function($exception, $code)
{
    // Log error
    Log::error($exception);

    switch ($code)
    {
        case 403:
            return Response::view('errors.403', array('exception' => $exception), 403);

        case 404:
            return Response::view('errors.404', array('exception' => $exception), 404);

        case 500:
            return Response::view('errors.500', array('exception' => $exception), 500);

        default:
            return Response::view('errors.default', array('exception' => $exception), $code);
    }
});