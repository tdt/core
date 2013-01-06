<?php 
/**
 * This class is returns the number of queries/errors made on/in the API/methods per day.
 *
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

class TDTInfoQueries extends AReader{

    
    private $queryResults;    

    public static function getParameters(){
	return array("package"  => "Name of a package that needs to be analysed, must be set !",
		     "resource" => "Name of a resource within the given package, is not required.",
                     "start"    => "The start of the time interval (in unix time) of which requests and errors are to be returned.",
                     "end"      => "The end of the time interval (in unix time) of which requests and errors are to be returned."
	);
    }

    public static function getRequiredParameters(){
        return array("package");
    }

    public function setParameter($key,$val){
        if($key == "package"){
            $this->package = $val;
            // the interface is resource, we'll handle it as resource_name, because resource is alrdy assigned as "Queries"
        }elseif($key == "resource"){
            $this->resource_name = $val;
        }elseif($key == "start"){
            if(is_numeric($val)){
                $this->start = $val;
            }else{
                throw new TDTException(452,array($key . " should be a unix time!."));
            }
        }elseif($key == "end"){
            if(is_numeric($val)){
                 $this->end = $val;
            }else{
                throw new TDTException(452,array($key . " should be a unix time!."));
            }
        }
    }

    private function getData() {
        
	
	// to make a correct query we need to find out if the resource is set, if not
	// we cannot use it in our query
	$clausule;
	$params = array();
	
        /**
         * decide which request and error query to ask
         */
        $requests;
        $errors;

        /**
         * Since PHP function overloading doesn't exist, we'll prepare the 
         * interval arguments so that we don't have too many functions just to get some
         * statistical data back
         */
        if(!isset($this->start)){
            $this->start = "";
        }
        if(!isset($this->end)){
            $this->end = "";
        }

	if(isset($this->resource_name) && $this->resource_name != ""){
	    $requests = DBQueries::getRequestsForResource($this->package,$this->resource_name,$this->start,$this->end);
            
            // create url to regex in errors
            $url = Config::get("general","hostname"). Config::get("general","subdir") . $this->package."/".$this->resource_name;
            $errors = DBQueries::getErrors($url,$this->start,$this->end);
	}else{            
	    $requests = DBQueries::getRequestsForPackage($this->package,$this->start,$this->end);
            $url = Config::get("general","hostname"). Config::get("general","subdir") . $this->package;
            $errors = DBQueries::getErrors($url,$this->start,$this->end);
	}
        
        
	/* there could be some gaps in our timeresults, which we don't want, even if there were 0 requests on a certain day,
	 * it's a good practice to actually return a 0 for that day. So we need to fill the time gaps.
	 */

	/*
	 * the redbeans layer returns an array with the pair amount and time as entry. i.e. 0 => [amount,time]
	 * we need a hash that maps time => amount, as is expected in the stats-page. A hash mapped on time => amount
	 * is also easier to work with. (i.e. to fill up gaps)
	 */
	$requestsmap = array();
	$errorsmap = array();
	$startdate = 9999999999;
	$enddate   = 0;
	
	foreach($requests as $pair){
	    if($pair["time"]<$startdate){
		$startdate = $pair["time"];
	    }
            if($pair["time"] >= $enddate){
		$enddate = $pair["time"];
	    }
	    $requestsmap[date("Y/m/d",$pair["time"])] =(int) $pair["amount"];
	}

	foreach($errors as $pair){
	    if($pair["time"]<$startdate){
		$startdate = $pair["time"];
	    }
            if($pair["time"] >= $enddate){
		$enddate = $pair["time"];
	    }
	    $errorsmap[date("Y/m/d",$pair["time"])] =(int) $pair["amount"];
	}

	$dates= array();
	$startdate = date("Y/m/d",$startdate);
	$enddate = date("Y/m/d",$enddate);
        
        
	$requests = array();
	$errors = array();

	$day = $startdate;
	
	while( $day <= $enddate){
	    // fill in the requests gap
            
	    if(!array_key_exists($day,$requestsmap)){
                $pair = new stdClass();
                $pair->time =(int)strtotime($day);
                $pair->amount = 0;
		array_push($requests,$pair);
	    }else{
                $pair = new stdClass();
                $pair->time =(int)strtotime($day);
                $pair->amount = $requestsmap[$day];
		array_push($requests,$pair);
	    }
	    
	    // fill in the errors gap
	    if(!array_key_exists($day,$errorsmap)){
                $pair = new stdClass();
                $pair->time =(int)strtotime($day);
                $pair->amount = 0;
		array_push($errors,$pair);
	    }else{
                $pair = new stdClass();
                $pair->time =(int)strtotime($day);
                $pair->amount = $errorsmap[$day];
		array_push($errors,$pair);
	    }
	    $unixday = strtotime($day);
	    $unixday+= 60*60*24;
	    $day = date("Y/m/d",$unixday);
	}
        
        
        $this->osort($requests,'time');
	$this->osort($errors,'time');
        
        $this->queryResults = new stdClass();
        $this->queryResults->requests = $requests;
        $this->queryResults->errors = $errors;
	
    }

    private function osort(&$array, $prop){
        usort($array, function($a, $b) use ($prop) {
                return $a->$prop > $b->$prop ? 1 : -1;
            }); 
    }

    public function read(){
         $this->getData();
        return $this->queryResults;
    }

    public static function getDoc(){
        return "Lists the number of queries(requests/errors) to this datatank instance per day";
    }

}
?>
