<?php 
/**
 * Lists all useragents for a certain package/resource in this The DataTank instance
 *
 * @package The-Datatank/packages/TDTStats
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

class TDTStatsUserAgents extends AReader{    

    public static function getParameters(){
	return array(
            "package" => "Statistics about this package (\"all\" selects all packages)",
            "resource" => "Statistics about this resource (\"all\" selects all packages)"
        );
    }

    public static function getRequiredParameters(){
        return array("package", "resource");
    }

    public function setParameter($key,$val){
        switch($key){
            case "package":
                $this->package = $val;
                break;
            case "resource":
                $this->resource = $val;
                break;
			// commented out because formatters can also have parameters
            //default:
            //    throw new ParameterTDTException($key);
        }
    }

    public function read(){
        //prepare arguments
        $arguments[":package"] = $this->package;
        $arguments[":resource"] = $this->resource;
        //prepare the where clause
        if($this->package == "all" && $this->resource = "all"){
            $clause = "1";
        }else if($this->package == "all"){
            $clause = "resource=:resource";
        }else if($this->resource == "all"){
            $clause = "package=:package";
        }else {
            $clause = "package=:package and resource=:resource";
        }

        $clause.= " AND request_method = 'GET'";

        $qresult = R::getAll(
            "SELECT count(1) as requests, user_agent
             FROM  requests
             WHERE $clause
             GROUP BY user_agent",
            $arguments
        );
        $qcount = R::getAll(
            "SELECT count(1) as rows
             FROM  requests
             WHERE $clause",
            $arguments
        );
        $result = array();
        //now we should have a percentage of every thing.
        foreach($qresult as $row){
            $result[$row["user_agent"]] = array();
            $result[$row["user_agent"]]["total"] = $row["requests"];
            $result[$row["user_agent"]]["percentage"] = round($row["requests"]/$qcount[0]["rows"] * 100, 2) ;
        }
        return $result;
    }

    public static function getDoc(){
        return "Lists all user agents for a certain package/resource in this The DataTank instance";
    }

}
?>
