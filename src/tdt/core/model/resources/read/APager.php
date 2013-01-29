<?php

/**
 * This abstract class forsees some datamembers and a function to implement RESTful paging
 * Note that in 
 * Returning objects of resources, or throwing an exception if something went wrong
 *
 * @package tdt\core\model\resources\read
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\read;

abstract class APager extends AReader{

	protected $DEFAULT_PAGE_SIZE = 50;

	public function __construct($package, $resource, $RESTparameters){
		parent::__construct($package, $resource, $RESTparameters);
	}

	/**
	 * setLinkHeader sets a Link header with next, previous
	 * @param int $limit  The limitation of the amount of objects to return
	 * @param int $offset The offset from where to begin to return objects (default = 0)
	 */
	protected function setLinkHeader($page,$referral = "next"){

		/**
		 * Process the correct referral options(next | previous)
		 */
		if($referral != "next" | $referral != "previous"){
			$referral = "next";
		}

		header("Link: ". Config::get("general","hostname") . Config::get("general","subdir") . $this->package . "/" . $this->resource . ".about?");

	}

}