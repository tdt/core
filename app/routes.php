<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

/**
 * First a hack that enables configuring TDT to live behind a proxy
 */

// we read a .env file as would be the case with Laravel 5
/*$env_file = dirname(__FILE__) . '/.env';

// parse this as properties defined in the .env file
$properties = parse_ini_file($env_file);

$proxy_url = $properties['PROXY_URL'];
$proxy_schema = $properties['PROXY_SCHEMA'];

if (!empty($proxy_url)) {
    URL::forceRootUrl($proxy_url);
}

if (!empty($proxy_schema)) {
    $proxy_schema = getenv('PROXY_SCHEMA');
}*/

/**
 * Admin routes
 */
Route::group(array('prefix' => 'api/admin'), function () {

    Route::any('', function () {
        // Redirect default admin page
        return Redirect::to('api/admin/datasets');
    });

    Route::get('login', 'Tdt\\Core\\Ui\\AuthController@getLogin');
    Route::post('login', 'Tdt\\Core\\Ui\\AuthController@postLogin');
    Route::get('logout', 'Tdt\\Core\\Ui\\AuthController@getLogout');

    Route::controller('settings', 'Tdt\\Core\\Ui\\SettingController');
    Route::controller('datasets', 'Tdt\\Core\\Ui\\DatasetController');
    Route::controller('users', 'Tdt\\Core\\Ui\\UserController');
    Route::controller('groups', 'Tdt\\Core\\Ui\\GroupController');

    Route::get('language/{lang}', 'Tdt\\Core\\Ui\\LanguageController@setLanguage');

    Route::any('{all}', 'Tdt\\Core\\Ui\\UiController@handleRequest')->where('all', '.*');
});

Route::any('/upload-file', function () {
    $utf8 = [
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u'    =>   '_', // Literally a single quote
        '/[“”«»„]/u'    =>   '_', // Double quote
        '/ /'           =>   '_', // nonbreaking space (equiv. to 0x160)
    ];

    $file_xslt_upload = Input::file('fileupload_xslt');

    if(isset($file_xslt_upload)) {
        $file_xslt = strtolower(preg_replace(array_keys($utf8), array_values($utf8), Input::file('fileupload_xslt')->getClientOriginalName()));
    }

    if(isset($file_xslt)){
        Input::file('fileupload_xslt')->move(
            app_path() . '/storage/app/',
            $file_xslt . '_' . date('Y-m-d') . '.' . Input::file('fileupload_xslt')->getClientOriginalExtension()
        );
    }

    if (! empty(Input::file('fileupload'))) {
        $file = strtolower(preg_replace(array_keys($utf8), array_values($utf8), Input::file('fileupload')->getClientOriginalName()));

        return Input::file('fileupload')->move(
            app_path() . '/storage/app/',
            $file . '_' . time() . '.' . Input::file('fileupload')->getClientOriginalExtension()
        );
    }
});

/* Autocomplete endpoint "Linking Datasets" */
Route::get('/search/autocomplete', 'Tdt\\Core\\Ui\\DatasetController@autocompleteLinkedDatasets');

/*
 * IMPORTANT!
 * The catch-all route to catch all other request is added last to allow packages to still have their own routes
 */
App::before(function () {
    // The (in)famous catch-all
    Route::any('{all}', 'Tdt\Core\BaseController@handleRequest')->where('all', '.*');
});

App::after(function ($request, $response) {
    // Remove cookie(s)
    $response->headers->removeCookie('tdt_auth');
    $response->headers->removeCookie('laravel_session');
});

/*
 * Proper error handling
 */
App::error(function ($exception, $code) {

    // Log error
    \Log::error($exception);

    // Check Accept-header
    $accept_header = \Request::header('Accept');
    $mimes = explode(',', $accept_header);

    if (in_array('text/html', $mimes) || in_array('application/xhtml+xml', $mimes)) {
        // Create HTML response, seperate templates for status codes
        switch ($code) {
            case 403:
                return Response::view('errors.403', array('exception' => $exception), 403);
                break;

            case 404:
                return Response::view('errors.404', array('exception' => $exception), 404);

            case 500:
                return Response::view('errors.500', array('exception' => $exception), 500);

            default:
                return Response::view('errors.default', array('exception' => $exception), $code);
        }
    } else {
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

App::finish(function ($request, $response) {
    $tracker_id = \Config::get('tracker.id');

    if (! empty($tracker_id)) {
        $tracker = \App::make('Tdt\Core\Analytics\TrackerInterface');
        $tracker->track($request, $tracker_id);
    }
});
