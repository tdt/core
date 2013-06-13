<?php
/**
 * This class handles a SHP file
 *
 * @package tdt/core/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lieven Janssen
 */
namespace tdt\core\strategies;


include_once(__DIR__ . "/../../../../includes/ShapeFile.inc.php");
include_once(__DIR__ . "/../../../../includes/proj4php/proj4php.php");

use tdt\exceptions\TDTException;
use tdt\core\model\resources\AResourceStrategy;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\core\utility\Config;

class SHP extends ATabularData {

    public function documentCreateParameters(){
        return array("uri" => "The path to the shape file (can be a url).",
                     "epsg" => "EPSG coordinate system code. Default to 4326.",
                     "columns" => "The columns that are to be published. By default it will publish all columns.",
                     "column_aliases" => "An array that contains the alias of a published column. This array should be build as column_name => column_alias. If no array is passed, the alias will be equal to the normal column name. If your column name,used as a key, contains whitespaces be sure to replace them with an underscore.",
                     "PK" => "The primary key for each row.",
        );
    }

    public function documentCreateRequiredParameters(){
        return array("uri");
    }

    public function documentReadRequiredParameters(){
        return array();
    }

    public function documentReadParameters(){
        return array();
    }

    protected function isValid($package_id,$generic_resource_id) {

        if(!isset($this->uri)){
            $this->throwException($package_id,$generic_resource_id, "Can't find uri of the Shape file");
        }

        if (!isset($this->columns)) {
            $this->columns = array();
        }

        if(!isset($this->column_aliases)){
            $this->column_aliases = array();
        }

        if (!isset($this->PK) && isset($this->pk)) {
            $this->PK = $this->pk;

        }else if(empty($this->PK)){
            $this->PK = "";
        }

        $uri = $this->uri;
        $columns = $this->columns;

        if (!is_dir("tmp")) {
            mkdir("tmp");
        }

        if(empty($this->columns)){
            $options = array('noparts' => false);
            $isUrl = (substr($uri , 0, 4) == "http");
            if ($isUrl) {
                $tmpFile = uniqid();
                file_put_contents("tmp/" . $tmpFile . ".shp", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".shp"));
                file_put_contents("tmp/" . $tmpFile . ".dbf", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".dbf"));
                file_put_contents("tmp/" . $tmpFile . ".shx", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".shx"));

                $shp = new \ShapeFile("tmp/" . $tmpFile . ".shp", $options); // along this file the class will use file.shx and file.dbf
            } else {
                $shp = new \ShapeFile($uri, $options); // along this file the class will use file.shx and file.dbf
            }

            $record = $shp->getNext();
            // read meta data
            if($record == false){
                exit();
            }

            $dbf_fields = $record->getDbfFields();
            $dataIndex = 0;
            foreach ($dbf_fields as $field) {
                $property = strtolower($field["fieldname"]);
                $this->columns[$dataIndex] = $property;
                $dataIndex++;
            }

            $shp_data = $record->getShpData();
            if(isset($shp_data['parts'])) {
                $this->columns[$dataIndex] = "coords";
            }
            if(isset($shp_data['x'])) {
                $this->columns[$dataIndex] = "lat";
                $this->columns[$dataIndex + 1] = "long";
            }

            unset($shp);
            if ($isUrl) {
                unlink("tmp/" . $tmpFile . ".shp");
                unlink("tmp/" . $tmpFile . ".dbf");
                unlink("tmp/" . $tmpFile . ".shx");
            }
        }
        return true;
    }

    public function read(&$configObject,$package,$resource) {

        // It may take a while for the SHP to be read.
        set_time_limit(1337);

        parent::read($configObject,$package,$resource);

        if(isset($configObject->uri)){
            $uri = $configObject->uri;
        }else{
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("The uri of the shape file can't be located ( $configObject->uri )."), $exception_config);
        }

        $columns = array();

        $PK = $configObject->PK;

        if(!empty($configObject->epsg)){
            $EPSG = $configObject->epsg;
        }

        $columns = $configObject->columns;

        $resultobject = new \stdClass();
        $arrayOfRowObjects = array();
        $row = 0;

        if (!is_dir("tmp")) {
            mkdir("tmp");
        }

        try {
            $options = array('noparts' => false);
            $isUrl = (substr($uri , 0, 4) == "http");
            if ($isUrl) {
                $tmpFile = uniqid();
                file_put_contents("tmp/" . $tmpFile . ".shp", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".shp"));
                file_put_contents("tmp/" . $tmpFile . ".dbf", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".dbf"));
                file_put_contents("tmp/" . $tmpFile . ".shx", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".shx"));

                $shp = new \ShapeFile("tmp/" . $tmpFile . ".shp", $options); // along this file the class will use file.shx and file.dbf
            } else {
                $shp = new \ShapeFile($uri, $options); // along this file the class will use file.shx and file.dbf
            }

            while ($record = $shp->getNext()) {

                // read meta data
                $rowobject = new \stdClass();
                $dbf_data = $record->getDbfData();

                foreach ($dbf_data as $property => $value) {
                    $property = strtolower($property);
                    if(in_array($property,$columns)) {
                        $rowobject->$property = trim($value);
                    }
                }

                if(in_array("coords",$columns) || in_array("lat",$columns)) {
                    // read shape data
                    $shp_data = $record->getShpData();

                    if (!empty($EPSG)) {
                        $proj4 = new \Proj4php();
                        $projSrc = new \Proj4phpProj('EPSG:'. $EPSG,$proj4);
                        $projDest = new \Proj4phpProj('EPSG:4326',$proj4);
                    }

                    if(isset($shp_data['parts'])) {

                        $parts = array();
                        foreach ($shp_data['parts'] as $part) {
                            $points = array();
                            foreach ($part['points'] as $point) {
                                $x = $point['x'];
                                $y = $point['y'];

                                if(!empty($EPSG)){

                                    $pointSrc = new \proj4phpPoint($x,$y);

                                    $pointDest = $proj4->transform($projSrc,$projDest,$pointSrc);
                                    $x = $pointDest->x;
                                    $y = $pointDest->y;
                                }

                                $points[] = $x.','.$y;
                            }
                            array_push($parts,implode(" ",$points));
                        }

                        $rowobject->coords = implode(';', $parts);
                    }

                    if(isset($shp_data['x'])) {
                        $x = $shp_data['x'];
                        $y = $shp_data['y'];

                        if (!empty($EPSG)) {
                            $pointSrc = new \proj4phpPoint($x,$y);
                            $pointDest = $proj4->transform($projSrc,$projDest,$pointSrc);
                            $x = $pointDest->x;
                            $y = $pointDest->y;
                        }

                        $rowobject->long = $x;
                        $rowobject->lat = $y;
                    }
                }

                if($PK == "") {
                    array_push($arrayOfRowObjects,$rowobject);
                } else {
                    if(!isset($arrayOfRowObjects[$rowobject->$PK]) && $rowobject->$PK != "") {
                        $arrayOfRowObjects[$rowobject->$PK] = $rowobject;
                    }elseif(isset($arrayOfRowObjects[$rowobject->$PK])){
                        // this means the primary key wasn't unique !
                        $log = new Logger('SHP');
                        $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                        $log->addAlert("$package/$resource : Primary key ". $rowobject->$PK . " isn't unique.");
                    }else{
                        // this means the primary key was empty, log the problem and continue
                        $log = new Logger('SHP');
                        $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                        $log->addAlert("$package/$resource : Primary key ". $rowobject->$PK . " is empty.");
                    }
                }
            }

            unset($shp);
            if ($isUrl) {
                unlink("tmp/" . $tmpFile . ".shp");
                unlink("tmp/" . $tmpFile . ".dbf");
                unlink("tmp/" . $tmpFile . ".shx");
            }
            return $arrayOfRowObjects;
        } catch( Exception $ex) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("The data could not be retrieved from the shape file with uri $configObject->uri."), $exception_config);
        }
    }
}