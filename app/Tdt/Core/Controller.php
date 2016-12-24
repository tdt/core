<?php

namespace Tdt\Core;

class Controller extends \Controller
{
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