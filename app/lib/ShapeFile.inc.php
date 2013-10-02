<?php
/**
 * This class is under GPL Licencense Agreement
 * @author Juan Carlos Gonzalez Ulloa <jgonzalez@innox.com.mx>
 * Innovacion Inteligente S de RL de CV (Innox)
 * Lopez Mateos Sur 2077 - Z16
 * Col. Jardines de Plaza del Sol
 * Guadalajara, Jalisco
 * CP 44510
 * Mexico
 *
 * Class to read SHP files and modify the DBF related information
 * Just create the object and all the records will be saved in $shp->records
 * Each record has the "shp_data" and "dbf_data" arrays with the record information
 * You can modify the DBF information using the $shp_record->setDBFInformation($data)
 * The $data must be an array with the DBF's row data.
 *
 * Performance issues:
 * Because the class opens and fetches all the information (records/dbf info)
 * from the file, the loading time and memory amount neede may be way to much.
 * Example:
 *   15 seconds loading a 210907 points shape file
 *   60Mb memory limit needed
 *   Athlon XP 2200+
 *   Mandrake 10 OS
 *
 *
 *
 * Edited by David Granqvist March 2008 for better performance on large files
 * This version only get the information it really needs
 * Get one record at a time to save memory, means that you can work with very large files.
 * Does not load the information until you tell it too (saves time)
 * Added an option to not read the polygon points can be handy sometimes, and saves time :-)
 * 
 * Example:
		
 //sets the options to show the polygon points, 'noparts' => true would skip that and save time
 $options = array('noparts' => false);
 $shp = new ShapeFile("../../php/shapefile/file.shp",$options); 

 //Dump the ten first records
 $i = 0;
 while ($record = $shp->getNext() and $i<10) {
 $dbf_data = $record->getDbfData();
 $shp_data = $record->getShpData();
 //Dump the information
 var_dump($dbf_data);
 var_dump($shp_data);
 $i++;
 }
 * 
 */

// Configuration
define("SHOW_ERRORS", true);
define("DEBUG", false);


// Constants
define("XY_POINT_RECORD_LENGTH", 16);


// Strings
define("ERROR_FILE_NOT_FOUND", "SHP File not found [%s]");
define("INEXISTENT_RECORD_CLASS", "Unable to determine shape record type [%i]");
define("INEXISTENT_FUNCTION", "Unable to find reading function [%s]");
define("INEXISTENT_DBF_FILE", "Unable to open (read/write) SHP's DBF file [%s]");
define("INCORRECT_DBF_FILE", "Unable to read SHP's DBF file [%s]");
define("UNABLE_TO_WRITE_DBF_FILE", "Unable to write DBF file [%s]");



class ShapeFile{
    private $file_name;
    private $fp;
    //Used to fasten up the search between records;
    private $dbf_filename = null;
    //Starting position is 100 for the records
    private $fpos = 100;

    private $error_message = "";
    private $show_errors   = SHOW_ERRORS;

    private $shp_bounding_box = array();
    private $shp_type         = 0;

    private $records;

    function ShapeFile($file_name,$options){
        $this->options = $options;

        $this->file_name = $file_name;
        //_d("Opening [$file_name]");
        if(!is_readable($file_name)){
            //return $this->setError( sprintf(ERROR_FILE_NOT_FOUND, $file_name) );
            throw new Exception(sprintf(ERROR_FILE_NOT_FOUND, $file_name));
        }

        $this->fp = fopen($this->file_name, "rb");
		
        $this->_fetchShpBasicConfiguration();

        //Set the dbf filename
        $this->dbf_filename = processDBFFileName($this->file_name);

    }
	
	
    public function getError(){
        return $this->error_message;
    }

	
    function __destruct()
    {
        $this->closeFile();
    }

    // Data fetchers
    private function _fetchShpBasicConfiguration(){
        //_d("Reading basic information");
        fseek($this->fp, 32, SEEK_SET);
        $this->shp_type = readAndUnpack("i", fread($this->fp, 4));
        //_d("SHP type detected: ".$this->shp_type);

        $this->shp_bounding_box = readBoundingBox($this->fp);
        ////_d("SHP bounding box detected: miX=".$this->shp_bounding_box["xmin"]." miY=".$this->shp_bounding_box["ymin"]." maX=".$this->shp_bounding_box["xmax"]." maY=".$this->shp_bounding_box["ymax"]);
    }



    public function getNext(){
        if (!feof($this->fp)) {
            fseek($this->fp, $this->fpos);
            $shp_record = new ShapeRecord($this->fp, $this->dbf_filename,$this->options);
            if($shp_record->getError() != ""){
                return false;
            }
            if($shp_record->record_number == "") {
                return false;			
            }
            $this->fpos = $shp_record->getNextRecordPosition();
            return $shp_record;
        }
        return false;
    }

    /* Takes too much memory
       function _fetchRecords(){
       fseek($this->fp, 100);
       while(!feof($this->fp)){
       $shp_record = new ShapeRecord($this->fp, $this->file_name);
       if($shp_record->error_message != ""){
       return false;
       }
       $this->records[] = $shp_record;
       }
       }
    */

//Not Used
/*	private function getDBFHeader(){
        $dbf_data = array();
        if(is_readable($dbf_data)){
        $dbf = dbase_open($this->dbf_filename , 1);
        // solo en PHP5 $dbf_data = dbase_get_header_info($dbf);
        echo dbase_get_header_info($dbf);
        }
	}
*/

    // General functions        
    private function setError($error){
        $this->error_message = $error;
        if($this->show_errors){
            echo $error."\n";
        }
        return false;
    }

    private function closeFile(){
        if($this->fp){
            fclose($this->fp);
        }
    }


}


/**
 * ShapeRecord
 *
 */    
class ShapeRecord{
    private $fp;
    private $fpos = null; 
	
    private $dbf = null;

    public $record_number     = null;
    private $content_length    = null;
    private $record_shape_type = null;

    private $error_message     = "";

    private $shp_data = array();
    private $dbf_data = array();

    private $file_name = "";

    private $record_class = array(  0 => "RecordNull",
                                    1 => "RecordPoint",
                                    8 => "RecordMultiPoint",
                                    3 => "RecordPolyLine",
                                    5 => "RecordPolygon");

    function ShapeRecord(&$fp, $file_name,$options){
        $this->fp = $fp;
        $this->fpos = ftell($fp);
        $this->options = $options;

        //_d("Shape record created at byte ".ftell($fp));
		
        if (feof($fp)) {
            echo "end ";
            exit;
        }
        $this->_readHeader();

        $this->file_name = $file_name;

    }
	
    public function getNextRecordPosition(){
        $nextRecordPosition = $this->fpos + ((4 + $this->content_length )* 2);
        return $nextRecordPosition;
    }

    private function _readHeader(){
        $this->record_number     = readAndUnpack("N", fread($this->fp, 4));
        $this->content_length    = readAndUnpack("N", fread($this->fp, 4));
        $this->record_shape_type = readAndUnpack("i", fread($this->fp, 4));
        //var_dump("Shape Record ID=".$this->record_number." ContentLength=".$this->content_length." RecordShapeType=".$this->record_shape_type."\nEnding byte ".ftell($this->fp)."\n");
        //_d("Shape Record ID=".$this->record_number." ContentLength=".$this->content_length." RecordShapeType=".$this->record_shape_type."\nEnding byte ".ftell($this->fp)."\n");
    }

    private function getRecordClass(){
        if(!isset($this->record_class[$this->record_shape_type])){
            //_d("Unable to find record class ($this->record_shape_type) [".getArray($this->record_class)."]");
            return $this->setError( sprintf(INEXISTENT_RECORD_CLASS, $this->record_shape_type) );
        }
        //_d("Returning record class ($this->record_shape_type) ".$this->record_class[$this->record_shape_type]);
        return $this->record_class[$this->record_shape_type];
    }

    private function setError($error){
        $this->error_message = $error;
        return false;
    }

    public function getError(){
        return $this->error_message;
    }

    public function getShpData(){
        $function_name = "read".$this->getRecordClass();

        //_d("Calling reading function [$function_name] starting at byte ".ftell($fp));

        if(function_exists($function_name)){
            $this->shp_data = $function_name($this->fp,$this->options);
        } else {
            $this->setError( sprintf(INEXISTENT_FUNCTION, $function_name) );
        }
		
        return $this->shp_data;
    }
	
    public function getDbfFields(){

        $fdbf = fopen($this->file_name,'r');
        $fields = array();
        $buf = fread($fdbf,32);
        $goon = true;
        while ($goon && !feof($fdbf)) { // read fields:
            $buf = fread($fdbf,32);
            if (substr($buf,0,1)==chr(13)) {$goon=false;} // end of field list
            else {
                $field=unpack( "a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf,0,18));
                array_push($fields, $field);
            }
        }
        fclose($fdbf);
        return $fields;
    }

    public function getDbfData(){
        $fdbf = fopen($this->file_name,'r');
        $buf = fread($fdbf,32);
        $header=unpack( "VRecordCount/vFirstRecord/vRecordLength", substr($buf,4,8));
        $goon = true;
        $unpackString='';
        while ($goon && !feof($fdbf)) { // read fields:
            $buf = fread($fdbf,32);
            if (substr($buf,0,1)==chr(13)) {$goon=false;} // end of field list
            else {
                $field=unpack( "a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf,0,18));
                $unpackString.="A$field[fieldlen]$field[fieldname]/";
            }
        }
        fseek($fdbf, $header['FirstRecord'] + 1 + ($header['RecordLength'] * ($this->record_number - 1)));        
        $buf = fread($fdbf,$header['RecordLength']);
        $record=unpack($unpackString,$buf);
        fclose($fdbf);
        return $record;
    }
}


/**
 * Reading functions
 */    

function readRecordNull(&$fp, $read_shape_type = false,$options = null){
    $data = array();
    if($read_shape_type) $data += readShapeType($fp);
    //_d("Returning Null shp_data array = ".getArray($data));
    return $data;
}
$point_count = 0;
function readRecordPoint(&$fp, $create_object = false,$options = null){
    global $point_count;
    $data = array();

    $data["x"] = readAndUnpack("d", fread($fp, 8));
    $data["y"] = readAndUnpack("d", fread($fp, 8));

    ////_d("Returning Point shp_data array = ".getArray($data));
    $point_count++;
    return $data;
}

function readRecordMultiPoint(&$fp,$options = null){
    $data = readBoundingBox($fp);
    $data["numpoints"] = readAndUnpack("i", fread($fp, 4));
    //_d("MultiPoint numpoints = ".$data["numpoints"]);

    for($i = 0; $i <= $data["numpoints"]; $i++){
        $data["points"][] = readRecordPoint($fp);
    }

    //_d("Returning MultiPoint shp_data array = ".getArray($data));
    return $data;
}

function readRecordPolyLine(&$fp,$options = null){
    $data = readBoundingBox($fp);
    $data["numparts"]  = readAndUnpack("i", fread($fp, 4));
    $data["numpoints"] = readAndUnpack("i", fread($fp, 4));

    //_d("PolyLine numparts = ".$data["numparts"]." numpoints = ".$data["numpoints"]);
    if (isset($options['noparts']) && $options['noparts']==true) {
        //Skip the parts
        $points_initial_index = ftell($fp)+4*$data["numparts"];
        $points_read = $data["numpoints"];
    }
    else{
        for($i=0; $i<$data["numparts"]; $i++){
            $data["parts"][$i] = readAndUnpack("i", fread($fp, 4));
            //_d("PolyLine adding point index= ".$data["parts"][$i]);
        }

        $points_initial_index = ftell($fp);

        //_d("Reading points; initial index = $points_initial_index");
        $points_read = 0;
        foreach($data["parts"] as $part_index => $point_index){
            //fseek($fp, $points_initial_index + $point_index);
            //_d("Seeking initial index point [".($points_initial_index + $point_index)."]");
            if(!isset($data["parts"][$part_index]["points"]) || !is_array($data["parts"][$part_index]["points"])){
                $data["parts"][$part_index] = array();
                $data["parts"][$part_index]["points"] = array();
            }
            while( ! in_array( $points_read, $data["parts"]) && $points_read < $data["numpoints"] && !feof($fp)){
                $data["parts"][$part_index]["points"][] = readRecordPoint($fp, true);
                $points_read++;
            }
        }
    }

    fseek($fp, $points_initial_index + ($points_read * XY_POINT_RECORD_LENGTH));

    //_d("Seeking end of points section [".($points_initial_index + ($points_read * XY_POINT_RECORD_LENGTH))."]");
    return $data;
}

function readRecordPolygon(&$fp,$options = null){
    //_d("Polygon reading; applying readRecordPolyLine function");
    return readRecordPolyLine($fp,$options);
}

/**
 * General functions
 */    
function processDBFFileName($dbf_filename){
    //_d("Received filename [$dbf_filename]");
    if(!strstr($dbf_filename, ".")){
        $dbf_filename .= ".dbf";
    }

    if(substr($dbf_filename, strlen($dbf_filename)-3, 3) != "dbf"){
        $dbf_filename = substr($dbf_filename, 0, strlen($dbf_filename)-3)."dbf";
    }
    //_d("Ended up like [$dbf_filename]");
    return $dbf_filename;
}

function readBoundingBox(&$fp){
    $data = array();
    $data["xmin"] = readAndUnpack("d",fread($fp, 8));
    $data["ymin"] = readAndUnpack("d",fread($fp, 8));
    $data["xmax"] = readAndUnpack("d",fread($fp, 8));
    $data["ymax"] = readAndUnpack("d",fread($fp, 8));

    //_d("Bounding box read: miX=".$data["xmin"]." miY=".$data["ymin"]." maX=".$data["xmax"]." maY=".$data["ymax"]);
    return $data;
}

function readAndUnpack($type, $data){
    if(!$data) return $data;
    return current(unpack($type, $data));
}

function _d($debug_text){
    if(DEBUG){
        echo $debug_text."\n";
    }
}

function getArray($array){
    ob_start();
    print_r($array);
    $contents = ob_get_contents();
    ob_get_clean();
    return $contents;
}
?>