<?php

namespace Tdt\Core;

use \Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;

/**
 * ApiController
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
abstract class ApiController extends \Controller
{

    protected $definition;

    public function __construct(DefinitionRepositoryInterface $definition)
    {
        $this->definition = $definition;
    }

    public function handle($uri)
    {

        $uri = ltrim($uri, '/');

        // Delegate the request based on the used http method
        $method = \Request::getMethod();

        switch ($method) {
            case "PUT":
                return $this->put($uri);
                break;
            case "GET":
                return $this->get($uri);
                break;
            case "POST":
            case "PATCH":
                return $this->patch($uri);
                break;
            case "DELETE":
                return $this->delete($uri);
                break;
            case "HEAD":
                return $this->head($uri);
                break;
            default:
                // Method not supported
                \App::abort(405, "The HTTP method '$method' is not supported by this resource ($uri).");
                break;
        }
    }

    public function get($uri)
    {
        \App::abort(405, "The HTTP method GET is not supported by this resource.");
    }

    public function put($uri)
    {
        \App::abort(405, "The HTTP method PUT is not supported by this resource.");
    }

    public function patch($uri)
    {
        \App::abort(405, "The HTTP method PATCH is not supported by this resource.");
    }

    public function head($uri)
    {
        \App::abort(405, "The HTTP method HEAD is not supported by this resource.");
    }

    public function delete($uri)
    {
        \App::abort(405, "The HTTP method DELETE is not supported by this resource.");
    }

    /**
     * Process the URI and return the extension (=format) and the resource identifier URI
     *
     * @param string $uri The URI that has been passed
     * @return array
     */
    public static function processURI($uri)
    {
        $dot_position = strrpos($uri, '.');

        if (!$dot_position) {
            return array($uri, null);
        }

        // If a dot has been found, do a couple
        // of checks to find out if it introduces a formatter
        $uri_parts = explode('.', $uri);

        $possible_extension = strtoupper(array_pop($uri_parts));

        $uri = implode('.', $uri_parts);

        $formatter_class = 'Tdt\\Core\\Formatters\\' . $possible_extension . 'Formatter';

        if (!class_exists($formatter_class)) {
            // Re-attach the dot with the latter part of the uri
            $uri .= '.' . strtolower($possible_extension);

            return array($uri, null);
        }

        return array($uri, strtolower($possible_extension));
    }
}
