<?php

namespace tdt\core\datacontrollers;

/**
 * CSV Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
abstract class ADataController {

    protected static $DEFAULT_PAGE_SIZE = 312;

    public abstract function readData($source_definitions, $rest_parameters = null);

    /**
     * Calculate the limit and offset based on the request string parameters.
     */
    protected function calculateLimitAndOffset(){

        $limit = \Input::get('limit', self::$DEFAULT_PAGE_SIZE);
        $offset = \Input::get('offset', 0);

        // Calculate the limit and offset, if only page and optionally page_size are given
        if($limit == self::$DEFAULT_PAGE_SIZE && $offset == 0){

            $page = \Input::get('page', 1);
            $page_size = \Input::get('page_size', self::$DEFAULT_PAGE_SIZE);

            // Don't do extra work when page and page_size are also default values
            if($page > 1 || $page_size != self::$DEFAULT_PAGE_SIZE){

                $offset = ($page -1)*$page_size;
                $limit = $page_size;
            }else if($page == -1){

                $limit = PHP_INT_MAX;
                $offset= 0;
            }
        }else if($limit == -1){
            $limit = PHP_INT_MAX;
        }

        return array($limit, $offset);
    }

    /**
     * Calculate the link headers.
     */
    protected function calculatePagingHeaders($limit, $offset, $total_rows){

        $paging = array();

        // Calculate the paging parameters and pass them with the data object
        if($offset + $limit < $total_rows){

            $page = $offset/$limit;
            $page = round($page, 0, PHP_ROUND_HALF_DOWN);

            if($page == 0){
                $page = 1;
            }

            $paging['next'] = array($page + 1, $limit);

            $last_page = round($total_rows / $limit,0);

            if($last_page > $page + 1){
                $paging['last'] = array($last_page, self::$DEFAULT_PAGE_SIZE);
            }
        }

        if($offset > 0 && $total_rows > 0){

            $page = $offset/$limit;
            $page = round($page, 0, PHP_ROUND_HALF_DOWN);

            if($page == 0){

                // Try to divide the paging into equal pages
                $page = 2;
            }

            $paging['previous'] = array($page - 1, $limit);
        }

        return $paging;
    }

    /**
     * Retrieve the optional request parameters, used in GET requests
     */
    public static function getParameters(){
        return array(
            'page' => array(
                        'required' => false,
                        'description' => "Represents the page number if the dataset is paged, this parameter can be used together with page_size, which is default set to 500. Set this parameter to -1 if you don't want paging to be applied.",
            ),
            'page_size' => array(
                        'required' => false,
                        'description' => "Represents the size of a page, this means that by setting this parameter, you can alter the amount of results that are returned, in one page (e.g. page=1&page_size=3 will give you results 1,2 and 3).",
            ),
            'limit' => array(
                        'required' => false,
                        'description' => "Instead of page/page_size you can use limit and offset. Limit has the same purpose as page_size, namely putting a cap on the amount of entries returned, the default is 500. Set this parameter to -1 if don't want paging to be applied.",
            ),
            'offset' => array(
                        'required' => false,
                        'description' => "Represents the offset from which results are returned (e.g. ?offset=12&limit=5 will return 5 results starting from 12)."
            ),
        );
    }
}
