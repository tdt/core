<?php

namespace tdt\core;

/**
 * The base controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class BaseController extends \Controller {

    /*
     * Handles all core requests
     */
    public function handleRequest($uri){

        // Introduce case insensitivity and trim right '/'
        $uri = strtolower(rtrim($uri, '/'));

        // Check first segment of the request
        switch(\Request::segment(1)){
            case 'discovery':
                // Discovery document
                $controller = 'tdt\core\definitions\DiscoveryController';
                break;
            // TODO: don't hardcode this part
            case 'definitions':
                // Definitions request
                $controller = 'tdt\core\definitions\DefinitionController';
                $uri = str_replace('definitions/', '', $uri);
                break;
            case 'info':
                // Info request
                $controller = 'tdt\core\definitions\InfoController';
                $uri = str_replace('info/', '', $uri);
                break;
            case 'discovery':
                // Discovery document
                $controller = 'tdt\core\definitions\DiscoveryController';
                break;
            case '':
                // Home URL requests
                $controller = 'tdt\core\HomeController';
                break;
            default:
                // None of the above -> must be a dataset request
                $controller = 'tdt\core\datasets\DatasetController';
                break;
        }

        $response = $controller::handle($uri);

        // Check the response type
        if($response instanceof \Illuminate\Http\RedirectResponse){

            // Redirect and that's it
            return $response;
        }else{

            // Regular response, add headers and forget Sentry's cookie

            // Forget authentication and cookie(s)
            \Sentry::logout();
            $cookie = \Cookie::forget('tdt_auth');

            // Make sure cross origin requests are allowed for GET
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET');

            return $response->withCookie($cookie);
        }
    }
}
