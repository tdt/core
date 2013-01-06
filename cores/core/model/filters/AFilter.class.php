<?php
  /**
   * This class is an abstract class for a filter instance.
   *
   * @package The-Datatank/filters
   * @copyright (C) 2011 by iRail vzw/asbl
   * @license AGPLv3
   * @author Jan Vansteenlandt
   */

abstract class AFilter{

    protected $params;
    
    /**
     * Constructor
     * @param array $params This array will contain the parameters necessary to apply the filter logic
     */
    public function __construct($params){
	$this->params = $params;
    }
    
     /**
     * This function will contain the filter logic
     * @param mixed  $result The result will be filtered and returned.
     */
    abstract function filter($result);
    

}
?>
