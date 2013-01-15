<?php
/**
 * This factory will provide Filters such as RESTFilter and SearchFilter
 *
 * @package The-Datatank/model/filters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\filters;

class FilterFactory{

	private static $factory;

	private function __construct(){

	}

	public static function getInstance(){
		if(!isset(self::$factory)){
			self::$factory = new FilterFactory();
		}
		return self::$factory;
	}

	/**
	 * Gets the filter, and passes along the necessary parameters in order to create that filter
	 * @param string $filter The filtername
	 * @param array $params The parameters necessary to instantiate the filter class
	 */
	public static function getFilter($filter,$params){
		$filter = "tdt\\core\\model\\filters\\". $filter;
		return new $filter($params);
	}

}
?>
