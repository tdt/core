<?php

namespace tdt\core\datasets;

/**
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
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
class Data {

    public $definition;

    public $source_definition;

    public $rest_parameters;

    public $paging;

    public $geo;

    public $data;

    /**
     * Property that states if data is a semantic structure
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
    public $formats;

    /**
     * Property used to construct the Link-Template header
     */
    public $template;
}
