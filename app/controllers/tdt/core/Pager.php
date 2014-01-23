<?php

namespace tdt\core;

/**
 * Pager class.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Pager{

    protected static $PAGING_KEYWORDS = array('next', 'last', 'previous', 'first');

    /**
     * Provide paging headers in the response using the Link HTTP header.
     */
    protected static function getLinkHeader($paging){

        $link_value = '';

        foreach($paging as $keyword => $page_info){

            if(!in_array($keyword, self::$PAGING_KEYWORDS)){

                $key_words = implode(', ', self::$PAGING_KEYWORDS);
                \App::abort(400, "The given paging keyword, $keyword, has not been found. Supported keywords are $key_words.");

            }else if(count($page_info) != 2){
                \App::abort(400, "The provided page info did not contain 2 parts, it should only contain a page number and a page size.");
            }

            $request_string = self::buildQuerystring();

            $link_value .= \Request::url() . '?offset=' . $page_info[0] . '&limit=' . $page_info[1] . $request_string .';rel=' . $keyword . ',';
        }

        // Trim the most right comma off.
        return rtrim($link_value, ",");
    }

    /**
     * Build the query string from the request
     *
     * If not empty, will return &a=b&c=d
     *
     * @return string
     */
    private static function buildQuerystring(){

        $request_params = \Request::all();
        $request_params = array_except($request_params, array('limit', 'offset'));
        $request_string = '';

        if(!empty($request_params)){
            $request_string = http_build_query($request_params);
            $request_string = '&' . $request_string;
        }

        return $request_string;
   }
}