<?php

namespace Tdt\Core\Datasets;

/**
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */

/**
 * This class is the internal datatank object.
 * It contains properties that add information about where
 * the data comes from, paging information and geo information.
 */
class Data
{
    /**
     * The definition configuration
     */
    public $definition;

    /**
     * The source definition configuration
     */
    public $source_definition;

    /**
     * Parameters that are part of the URI
     */
    public $rest_parameters = array();

    /**
     *  Parameters that are part of the query string
     */
    public $optional_parameters = array();

    /**
     * Contains paging information about the requested resource
     */
    public $paging;

    /**
     * Contains geo information about the requested resource
     */
    public $geo;

    /**
     * The raw data object (in PHP or an EasyRdf_Graph, the is_semantic property should be true if so)
     */
    public $data;

    /**
     * Property that states if data is a semantic structure ( = EasyRdf_Graph )
     */
    public $is_semantic = false;

    public $is_spectql = false;

    /**
     * List of prefixes with their corresponding URI
     */
    public $semantic;

    /**
     * Property used to list all the available formats that are applicable to the data
     */
    public $formats = array();

    /**
     * Property used to list the formats in a prioritized way
     */
    public $preferred_formats = array();
}
