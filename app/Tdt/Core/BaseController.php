<?php

namespace Tdt\Core;

/**
 * The base controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class BaseController extends Controller
{
    /*
     * Handles all core requests
     */
    public function handleRequest($uri)
    {
        // Save the original uri for controllers who don't want the uri lowercased
        $original_uri = rtrim($uri, '/');

        // Introduce case insensitivity and trim right '/'
        $uri = strtolower(rtrim($uri, '/'));

        // Check first segment of the request
        switch (\Request::segment(1)) {
            case 'discovery':
                // Discovery document
                $controller = 'Tdt\\Core\\Definitions\\DiscoveryController';
                break;
            case 'api':
                // Allow for content negotiation for api endpoints
                list($apiResource, $extension) = self::processURI(\Request::segment(2));

                switch ($apiResource) {
                    case 'definitions':
                        // Definitions request
                        $controller = 'Tdt\\Core\\Definitions\\DefinitionController';
                        $uri = str_replace('api/definitions', '', $uri);
                        break;
                    case 'info':
                        // Info request
                        $controller = 'Tdt\\Core\\Definitions\\InfoController';
                        $uri = str_replace('api/info', '', $uri);
                        break;
                    case 'dcat':
                        // Dcat request
                        $controller = 'Tdt\\Core\\Definitions\\DcatController';
                        $uri = str_replace('api/dcat', '', $uri);
                        break;
                    case 'languages':
                        // Supported languages request
                        $controller = 'Tdt\\Core\\Definitions\\LanguageController';
                        $uri = str_replace('api/languages', '', $uri);
                        break;
                    case 'geoprojections':
                        $controller = 'Tdt\\Core\\Definitions\\GeoprojectionController';
                        $uri = str_replace('api/geoprojections', '', $uri);
                        break;
                    case 'licenses':
                        // Supported licenses request
                        $controller = 'Tdt\\Core\\Definitions\\LicenseController';
                        $uri = str_replace('api/licenses', '', $uri);
                        break;
                    case 'prefixes':
                        // Supported prefixes request
                        $controller = 'Tdt\\Core\\Definitions\\OntologyController';
                        $uri = str_replace('api/prefixes', '', $uri);
                        break;
                    case 'themes':
                        // Supported themes request
                        $controller = 'Tdt\\Core\\Definitions\\ThemeController';
                        $uri = str_replace('api/themes', '', $uri);
                        break;
                    case 'keywords':
                        $controller = 'Tdt\\Core\\Definitions\\KeywordController';
                        $uri = str_replace('api/keywords', '', $uri);
                        break;
                    default:
                        \App::abort(404, 'Page not found.');
                        break;
                }

                break;
            case '':
                // Home URL requests
                $controller = 'Tdt\\Core\\HomeController';
                break;
            default:
                // None of the above -> must be a dataset request
                $controller = 'Tdt\\Core\\Datasets\\DatasetController';
                break;
        }

        $controller = \App::make($controller);

        $response = $controller->handle($uri);

        // Check the response type
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            // Redirect and that's it
            return $response;
        } elseif ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            return $response;
        } else {
            // Make sure cross origin requests are allowed for GET
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE');

            return $response;
        }
    }

    /**
     * Process the URI and return the extension (=format) and the resource identifier URI
     *
     * @param  string $uri The URI that has been passed
     * @return array
     */
    public static function processURI($uri)
    {
        $dot_position = strrpos($uri, '.');

        if (! $dot_position) {
            return array($uri, null);
        }

        // If a dot has been found, do a couple
        // of checks to find out if it introduces a formatter
        $uri_parts = explode('.', $uri);

        $possible_extension = strtoupper(array_pop($uri_parts));

        $uri = implode('.', $uri_parts);

        $formatter_class = 'Tdt\\Core\\Formatters\\' . $possible_extension . 'Formatter';

        if (! class_exists($formatter_class)) {
            // Re-attach the dot with the latter part of the uri
            $uri .= '.' . strtolower($possible_extension);

            return array($uri, null);
        }

        return array($uri, strtolower($possible_extension));
    }
}
