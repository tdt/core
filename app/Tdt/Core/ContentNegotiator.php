<?php

namespace Tdt\Core;

use Tdt\Core\Formatters\FormatHelper;

/**
 * Content negotiator
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class ContentNegotiator extends Pager
{

    /**
     * Map MIME-types on formatters for Accept-header
     */
    public static $mime_types_map = array(
        'text/csv' => 'CSV',
        'text/html' => 'HTML',
        'application/json' => 'JSON',
        'application/xml' => 'XML',
        'application/xslt+xml' => 'XML',
        'application/xhtml+xml' => 'HTML',
    );

    /**
     * Format using requested formatter (via extension, Accept-header or default)
     */
    public static function getResponse($data, $extension = null)
    {

        // Check Accept-header
        $accept_header = \Request::header('Accept');

        // Extract the accept parts
        $mime_types = explode(',', $accept_header);

        // Extension has priority over Accept-header
        if (empty($extension)) {
            foreach ($mime_types as $mime) {

                if (!empty(ContentNegotiator::$mime_types_map[$mime])) {
                    // Matched mime type
                    $extension = ContentNegotiator::$mime_types_map[$mime];
                    break;
                }
            }

            // Still nothing? Use default formatter
            if (empty($extension) && empty($data->semantic)) {
                // Default formatter for non semantic data
                $extension = 'json';
            } elseif (empty($extension) && !empty($data->semantic)) {
                // Default formatter for semantic data is turtle
                $extension = 'ttl';
            }
        }

        // Safety first
        $extension = strtoupper($extension);

        // Formatter class
        $formatter_class = 'Tdt\\Core\\Formatters\\' . $extension . 'Formatter';

        if (!class_exists($formatter_class)) {

            // Use default formatter if */*;q=0.0 Accept header is not set
            if(in_array('*/*;q=0.0', $mime_types)){

                $format_helper = new FormatHelper();

                $available_formats = implode(', ', array_values($format_helper->getAvailableFormats($data)));
                \App::abort(406, "The requested Content-Type is not supported, the supported formats for this resource are: " . $available_formats);
            }else{
                if(empty($data->semantic)){
                    // Default formatter for non semantic data
                    $extension = 'json';
                }else{
                    $extension = 'ttl';
                }
            }

            // Safety first
            $extension = strtoupper($extension);

            // Formatter class
            $formatter_class = 'Tdt\\Core\\Formatters\\' . $extension . 'Formatter';
        }


        // Create the response from the designated formatter
        $response = $formatter_class::createResponse($data);

        // Set the paging headers
        if (!empty($data->paging)) {
            $response->header('Link', self::getLinkHeader($data->paging));
        }

        // Cache settings
        $cache_minutes = -1;

        if (\Config::get('cache.enabled', true)) {
            $cache_minutes = 1;

            // Cache per resource
            if (!empty($data->source_definition)) {
                $cache_minutes = $data->source_definition['cache'];
            }
        }

        // Cache headers
        $response->header('Cache-Control', 'public, max-age='. $cache_minutes*60 .', pre-check='. $cache_minutes*60 .'');
        $response->header('Pragma', 'public');
        $response->header('Expires', date(DATE_RFC822, strtotime("$cache_minutes minute")));

        // Return formatted response
        return $response;
    }
}
