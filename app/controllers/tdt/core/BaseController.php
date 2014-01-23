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

        // Save the original uri for controllers who don't want the uri lowercased
        $original_uri = rtrim($uri, '/');

        // Introduce case insensitivity and trim right '/'
        $uri = strtolower(rtrim($uri, '/'));

        // Check first segment of the request
        switch(\Request::segment(1)){
            case 'discovery':
                // Discovery document
                $controller = 'tdt\core\definitions\DiscoveryController';
                break;
            // TODO: don't hardcode this part
            case 'api':
                switch(\Request::segment(2)){

                    case 'definitions':
                        // Definitions request
                        $controller = 'tdt\core\definitions\DefinitionController';
                        $uri = str_replace('api/definitions', '', $uri);
                        break;
                    case 'info':
                        // Info request
                        $controller = 'tdt\core\definitions\InfoController';
                        $uri = str_replace('api/info', '', $uri);
                        break;
                    case 'dcat':
                        // Dcat request
                        $controller = 'tdt\core\definitions\DcatController';
                        $uri = str_replace('api/dcat', '', $uri);
                        break;
                    case 'languages':
                        // Supported languages request
                        $controller = 'tdt\core\definitions\LanguageController';
                        $uri = str_replace('api/languages', '', $uri);
                        break;
                    case 'licenses':
                        // Supported licenses request
                        $controller = 'tdt\core\definitions\LicenseController';
                        $uri = str_replace('api/licenses', '', $uri);
                        break;
                    case 'prefixes':
                        // Supported prefixes request
                        $controller = 'tdt\core\definitions\OntologyController';
                        $uri = str_replace('api/prefixes', '', $uri);
                        break;
                    default:
                        \App::abort(404, "Page not found.");
                        break;
                }

                break;
            case 'discovery':
                // Discovery document
                $controller = 'tdt\core\definitions\DiscoveryController';
                break;
            case 'spectql':
                // SPECTQL request
                $uri = str_ireplace('spectql', '', $original_uri);
                $controller = 'tdt\core\definitions\SpectqlController';
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
            \Sentry::logout();

            // Make sure cross origin requests are allowed for GET
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE');

            return $response;
        }
    }
}
