<?php

namespace Tdt\Core\DataControllers;

/**
 * CSV Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
abstract class ADataController
{

    protected static $DEFAULT_PAGE_SIZE = 350;

    abstract public function readData($source_definitions, $rest_parameters = array());

    /**
     * Retrieve the optional request parameters, used in GET requests
     */
    public static function getParameters()
    {
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

    /**
     * Provide an array a formatter priorities
     */
    protected function getPreferredFormats()
    {
        // Both semantic and raw data structures support json
        return array('json');
    }
}
