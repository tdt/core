<?php 
/**
 * This class is returns the number of queries/errors made on/in the API/methods on a certain month.
 *
 * @package The-Datatank/packages/TDTStats
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Lieven Janssen <lieven.janssen@okfn.org> 
 */

class TDTStatsMonth extends AReader{    

    public static function getParameters(){
	return array(
            "package" => "Statistics about this package (\"all\" selects all packages, \"total\" summarize packages)",
            "resource" => "Statistics about this resource (\"all\" selects all resources, \"total\" summarize resources)",
            "year" => "Year in XXXX format (\"all\" selects all years, \"total\" summarize years)",
            "month" => "Month with trailing 0: 01 is January (\"all\" selects all months, \"total\" summarize months)"
        );
    }

    public static function getRequiredParameters(){
        return array("package", "resource","year","month");
    }

    public function setParameter($key,$val){
        switch($key){
            case "package":
                $this->package = $val;
                break;
            case "resource":
                $this->resource = $val;
                break;
            case "year":
                $this->year = $val;
                break;
            case "month":
                $this->month = $val;
                break;
            
			// commented out because formatters can also have parameters
            //default:
            //    throw new ParameterTDTException($key);
        }
    }

    public function read(){
        $selectarr = array();
        $wherearr = array();
        $groupbyarr = array();     
        
        //prepare arguments
        $arguments[":package"] = $this->package;
        $arguments[":resource"] = $this->resource;
        $arguments[":year"] = $this->year;
        $arguments[":month"] = $this->month;

        //prepare the clauses
        switch($this->package) {
            case "all":
                array_push($selectarr,"package");
                array_push($groupbyarr,"package");
                unset($arguments[":package"]);
                break;
            case "total":
                array_push($selectarr,"'total' as package");
                unset($arguments[":package"]);
                break;
            default:
                array_push($selectarr,"package");
                array_push($wherearr,"package=:package");
                array_push($groupbyarr,"package");
                break;
        }
        
        switch($this->resource) {
            case "all":
                array_push($selectarr,"resource");
                array_push($groupbyarr,"resource");
                unset($arguments[":resource"]);
                break;
            case "total":
                array_push($selectarr,"'total' as resource");
                unset($arguments[":resource"]);
                break;
            default:
                array_push($selectarr,"resource");
                array_push($wherearr,"resource=:resource");
                array_push($groupbyarr,"resource");
                break;
        }

        switch($this->year) {
            case "all":
                array_push($selectarr,"from_unixtime(time, '%Y') as year");
                array_push($groupbyarr,"from_unixtime(time, '%Y')");
                unset($arguments[":year"]);
                break;
            case "total":
                array_push($selectarr,"'total' as year");
                unset($arguments[":year"]);
                break;
            default:
                array_push($selectarr,"from_unixtime(time, '%Y') as year");
                array_push($wherearr,"from_unixtime(time,'%Y')=:year");
                array_push($groupbyarr,"from_unixtime(time, '%Y')");
                break;        
        }
        
        switch($this->month) {
            case "all":
                array_push($selectarr,"from_unixtime(time, '%M') as month");
                array_push($groupbyarr,"from_unixtime(time, '%M')");
                unset($arguments[":month"]);
                break;
            case "total":
                array_push($selectarr,"'total' as month");
                unset($arguments[":month"]);
                break;
            default:
                array_push($selectarr,"from_unixtime(time, '%M') as month");
                array_push($wherearr,"from_unixtime(time,'%m')=:month");
                array_push($groupbyarr,"from_unixtime(time, '%M')");
                break;        
        }    
        
        $selectclause = implode(", ",$selectarr);
        $whereclause = implode(" and ",$wherearr);
        $groupbyclause = implode(", ",$groupbyarr);
        
        $selectclause .= ", count(1) as requests";
        if ($whereclause != "") {
            $whereclause = "WHERE " . $whereclause . " AND request_method = 'GET'";
        }
        if ($groupbyclause != "") {
            $groupbyclause = "GROUP BY " . $groupbyclause;
        }

        //To be considered: should we cache this?
        $qresult = R::getAll(
            "SELECT $selectclause
             FROM  requests 
             $whereclause
             $groupbyclause",
            $arguments
        );

        $result = array();
        //TODO: fill the gaps
        foreach($qresult as $row){
            $result[] = array(
                "package" => $row["package"],
                "resource" => $row["resource"],
                "year" => $row["year"],
                "month" => $row["month"],
                "requests" => $row["requests"],
                //"useragent" => "nyimplemented",
                //"errors" => "nyimplemented",
                //"languages" => "nyimplemented"
            );
        }
        return $result;
    }

    public static function getDoc(){
        return "Lists statistics about a certain month in the history of this The DataTank instance";
    }

}
?>
