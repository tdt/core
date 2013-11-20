<?php

namespace tdt\core;

/**
 * Content negotiator
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class ContentNegotiator extends Pager{

    /**
     * Map MIME-types on formatters for Accept-header
     */
    public static $mime_types_map = array(
        'text/csv' => 'CSV',
        'application/json' => 'JSON',
        'application/xml' => 'XML',
        'application/xslt+xml' => 'XML',
    );

    /**
     * Format using requested formatter (via extension, Accept-header or default)
     */
    public static function getResponse($data, $extension = null){

        // Extension has priority over Accept-header
        if(empty($extension)){

            // Check Accept-header
            $accept_header = \Request::header('Accept');

            $mimes = explode(',', $accept_header);
            foreach($mimes as $mime){
                if(!empty(ContentNegotiator::$mime_types_map[$mime])){
                    // Matched mime type
                    $extension = ContentNegotiator::$mime_types_map[$mime];
                    break;
                }
            }

            // Still nothing? Use default formatter
            if(!$extension && empty($data->semantic)){
                // TODO: get default formatter from config
                $extension = 'HTML';
            }else if(empty($extension) && !empty($data->semantic)){
                // Default formatter for semantic data is turtle
                $extension = 'ttl';
            }
        }

        // Safety first
        $extension = strtoupper($extension);

        // Formatter class
        $formatter_class = '\\tdt\\core\\formatters\\'.$extension.'Formatter';

        if(!class_exists($formatter_class)){
            \App::abort(400, "Formatter $extension doesn't exist.");
        }

        // Create the response from the designated formatter
        $response = $formatter_class::createResponse($data);

        // Set the paging headers
        if(!empty($data->paging)){
            $response->header('Link', self::getLinkHeader($data->paging));
        }

        // Return formatted response
        return $response;
    }

}
