<?php
/**
 * Apache Log Parser
 * Parses an Apache log file and runs the strings through filters to find what you're looking for.
 * Code was legally stolen from http://blog.ericlamb.net/2010/01/parse-apache-log-files-with-php/
 * and adjusted with proper regular expressions as the ones provided in the original file didn't work
 * for a CLF apache log file.(standard logging format)
 * 
 * @author Eric Lamb (base code)
 * @author Jan Vansteenlandt (adjustments & fixes)
 *
 */

namespace tdt\core\utility;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\core\utility\Config;
use tdt\exceptions\TDTException;

class ApacheLogParser{

	/*
	 * The default limit amount of hits to put into the resulting array
	 */
	public static $DEFAULT_LIMIT = 50;

	/**
	 * The path to the log file
	 * @var string
	 */
	private $file = FALSE;

	/**
	 * What filters to apply. Should be in the format of array('KEY_TO_SEARCH' => array('regex' => 'YOUR_REGEX'))
	 * @var array
	 */
	public $filters = FALSE;

	/*
	 * What formatting regular expression to use, currently supported are the common and combined logging format
	 * @var string
	 */
	private $format;

	/*
	 * Regular expressions for the supported formats
	 * @var $array
	 */
	private $regular_expressions = array(
			'common' => "/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) (.*) (.*) \[(.*):(\d{2}:\d{2}:\d{2}) (.*\d{4})\] \"(.*) (.*) (.*\/.*)\" (\d{3}) (.*)$/",
			'combined' => "/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) (.*) (.*) \[(.*):(\d{2}:\d{2}:\d{2}) (.*\d{4})\] \"(.*) (.*) (.*\/.*)\" (\d{3}) (.*) \"(.*)\" \"(.*)\"$/"
		);

	/**
	 * Constructor
	 * @param string $file
	 * @param string format The format the apace access.log is formatted by
	 * @return void
	 */
	public function __construct($file, $format="common"){
		if(!is_readable($file)){
			throw new TDTException(500,array("Logfile given, $file, could not be resolved or found."));
		}

		$this->format = $format;
		$this->file = $file;
	}

	/**
	 * Executes the supplied filter to the string
	 * In the blog it will return the string when 1 of the regex's
	 * complies, this adjusted function will return the string if it
	 * complies to all the passed filters!
	 * @param $filer	 
	 * @return string
	 */
	private function applyFilters($str){
		if(!$this->filters || !is_array($this->filters)){
			return $str;
		}	

		$str_passed_filters = true;
		foreach($this->filters AS $area => $filter){				
			if(!preg_match($filter['regex'], $str[$area], $matches, PREG_OFFSET_CAPTURE)){
				$str_passed_filters = false;
			}
		}

		if($str_passed_filters){			
			return $str;
		}		
	}

	/**
	 * Returns an array of all the filtered lines 
	 * @param $limit
	 * @param $offset
	 * @return array
	 */
	public function getData($limit = 50, $offset = 1){
		$handle = fopen($this->file, 'rb');
		if ($handle) {
			$count = 0;
			$lines = array();
			while (!feof($handle)) {
				$buffer = fgets($handle);	
				// if line within limit, offset scope
					
				$data = $this->applyFilters($this->formatLine($buffer));

				if($data){
					// count amount of hits.
					$count++;

					// if the count is in the limit and offset, add it to the result array
					if($count >= $offset && $count < $limit + $offset -1){	
						$lines[] = $data;
					}
				}											
			}
		}		
		fclose($handle);

		$resultObject = new \stdClass();
		$resultObject->hits = $count;
		$resultObject->lines = $lines;
		return $resultObject;		
	}

	/**
	 * Regex to parse the log file line
	 * @param string $line
	 * @return array
	 */
	function formatLogLine($line){
		$regex = $this->regular_expressions[$this->format];		
		preg_match($regex, $line, $matches); // pattern to format the line
		if(count($matches)==0){
			$log = new Logger('ApacheLogParser');
    		$log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::WARNING));
    		$log->addWarning("No matches found, this could be because you didn't configure the logging format of your apache correctly in the passed configuration. Currently using format: $this->format.");
		}
		return $matches;
	}

	/**
	 * Takes the format_log_line array and makes it usable to us stupid humans
	 * @param $line
	 * @return array
	 */
	function formatLine($line){
		$logs = $this->formatLogLine($line); // format the line		
		if (isset($logs[0])){
			$formated_log = array(); // make an array to store the lin info in
			$formated_log['ip'] = $logs[1];
			$formated_log['identity'] = $logs[2];
			$formated_log['user'] = $logs[2];
			$formated_log['date'] = $logs[4];
			$formated_log['time'] = $logs[5];
			$formated_log['timezone'] = $logs[6];
			$formated_log['method'] = $logs[7];
			$formated_log['path'] = $logs[8];
			$formated_log['protocol'] = $logs[9];
			$formated_log['status'] = $logs[10];
			$formated_log['bytes'] = $logs[11];
			if($this->format == "combined"){
				$formated_log['referer'] = $logs[12];
				$formated_log['agent'] = $logs[13];
			}
			
			return $formated_log; // return the array of info
		}
		else{
			$this->badRows++; // if the row is not in the right format add it to the bad rows
			return false;
		}
	}
}