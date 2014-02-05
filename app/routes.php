<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

/**
 * Admin routes
 */
Route::group(array('prefix' => 'api/admin'), function(){

    Route::any('', function(){
        // Redirect default admin page
        return Redirect::to('api/admin/datasets');
    });

    Route::controller('datasets', 'tdt\core\ui\DatasetController');
    Route::controller('users', 'tdt\core\ui\UserController');
    Route::controller('groups', 'tdt\core\ui\GroupController');
});

/*
 * IMPORTANT!
 * The catch-all route to catch all other request is added last to allow packages to still have their own routes
 */
App::before(function(){
    // The (in)famous catch-all
    Route::any('{all}', 'tdt\core\BaseController@handleRequest')->where('all', '.*');
});

App::after(function($request, $response){
    // Remove cookie(s)
    $response->headers->removeCookie( 'tdt_auth' );
    $response->headers->removeCookie( 'laravel_session' );
});

/*
 * Proper error handling
 */
App::error(function($exception, $code){

    // Log error
    Log::error($exception);

    // Check Accept-header
    $accept_header = \Request::header('Accept');
    $mimes = explode(',', $accept_header);

    if(in_array('text/html', $mimes) || in_array('application/xhtml+xml', $mimes)){

        // Create HTML response, seperate templates for status codes
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
    }else{

        // Display a JSON error
        $error_json = new stdClass();
        $error_json->error = new stdClass();

        // TODO: Set error type based on status code
        switch ($code) {
            case 500:
                $error_json->error->type = 'api_error';
                break;

            default:
                $error_json->error->type = 'invalid_request_error';
                break;
        }

        $error_json->error->message = $exception->getMessage();

        // Create response
        $response =  Response::json($error_json);
        $response->setStatusCode($code);

        // Make sure cross origin requests are allowed
        $response->header('Access-Control-Allow-Origin', '*');

        return $response;
    }

});