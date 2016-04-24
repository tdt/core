<?php

namespace Tdt\Core;

/**
 * Pager class.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Pager
{
    protected static $PAGING_KEYWORDS = array(
                    'next' => 'http://www.w3.org/ns/hydra/core#nextPage',
                    'last' => 'http://www.w3.org/ns/hydra/core#lastPage',
                    'previous' => 'http://www.w3.org/ns/hydra/core#previousPage',
                    'first' => 'http://www.w3.org/ns/hydra/core#firstPage'
                    );

    protected static $DEFAULT_PAGE_SIZE = 500;

    /**
     * Provide paging headers in the response using the Link HTTP header.
     */
    protected static function getLinkHeader($paging)
    {
        $link_value = '';

        foreach ($paging as $keyword => $page_info) {
            if (!in_array($keyword, array_keys(self::$PAGING_KEYWORDS))) {
                $key_words = implode(', ', array_keys(self::$PAGING_KEYWORDS));
                \App::abort(400, "The given paging keyword, $keyword, has not been found. Supported keywords are $key_words.");

            } elseif (count($page_info) != 2) {
                \App::abort(400, "The provided page info did not contain 2 parts, it should only contain a page number and a page size.");
            }

            $request_string = self::buildQuerystring();

            $link_value .= \Request::url() . '?offset=' . $page_info[0] . '&limit=' . $page_info[1] . $request_string .';rel=' . self::$PAGING_KEYWORDS[$keyword] . ',';
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
    public static function buildQuerystring()
    {
        $request_params = \Request::all();
        $request_params = array_except($request_params, array('limit', 'offset'));
        $request_string = '';

        if (!empty($request_params)) {
            $request_string = http_build_query($request_params);
            $request_string = '&' . $request_string;
        }

        return $request_string;
    }

    /**
     * Calculate the link meta-data for paging purposes, return an array with paging information
     *
     * @param integer $limit
     * @param integer $offset
     * @param integer $total_rows The total amount of objects
     *
     * @return array
     */
    public static function calculatePagingHeaders($limit, $offset, $total_rows)
    {
        $paging = array();

        // Check if limit and offset are integers
        if (!is_integer((int)$limit) || !is_integer((int)$offset)) {
            \App::abort(400, "Please make sure limit and offset are integers.");
        }

        // Calculate the paging parameters and pass them with the data object
        if ($offset + $limit < $total_rows) {
            $paging['next'] = array($limit + $offset, (int)$limit);

            $last_page = ceil($total_rows / $limit);

            $paging['last'] = array(($last_page) * $limit, (int)$limit);
        }

        if ($offset > 0 && $total_rows > 0) {
            $previous = $offset - $limit;
            if ($previous < 0) {
                $previous = 0;
            }

            $paging['previous'] = array($previous, $limit);
        }

        return $paging;
    }

    /**
     * Calculate the limit and offset based on the request string parameters.
     */
    public static function calculateLimitAndOffset($limit = null)
    {
        if (empty($limit)) {
            $limit = self::$DEFAULT_PAGE_SIZE;
        }

        $limit = \Input::get('limit', $limit);
        $offset = \Input::get('offset', 0);

        // Calculate the limit and offset, if only page and optionally page_size are given
        $page = \Input::get('page', 1);
        if ($offset == 0 && $page > 1) {
            $page_size = \Input::get('page_size', $limit);

            // Don't do extra work when page and page_size are also default values
            if ($page > 1 || $page_size != $limit) {
                $offset = ($page -1)*$page_size;
                $limit = $page_size;
            } elseif ($page == -1) {
                $limit = PHP_INT_MAX;
                $offset= 0;
            }
        } elseif ($limit == -1) {
            $limit = PHP_INT_MAX;
        }

        return array((int)$limit, (int)$offset);
    }
}
