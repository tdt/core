<?php

namespace tdt\core;

class ContentNegotiator{

    /**
     * Format using requested formatter (via extension, Accept header or default)
     */
    public static function getResponse($data, $extension = null){

        // TODO: check accept header
        //\Request::header('Accept');

        // TODO: default formatter

        // Formatter class
        $formatter_class = '\\tdt\\core\\formatters\\'.$extension.'Formatter';

        if(!class_exists($formatter_class)){
            \App::abort(400, "Formatter $extension doesn't exist.");
        }

        // Return formatted response
        return $formatter_class::createResponse($data);
    }

}