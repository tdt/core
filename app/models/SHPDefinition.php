<?php


// PHP SHP libraries arent PSR-0 yet.
include_once(__DIR__ . "/../lib/ShapeFile.inc.php");
include_once(__DIR__ . "/../lib/proj4php/proj4php.php");

/**
 * Shape definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class ShpDefinition extends SourceType{

    protected $table = 'shpdefinitions';

    protected $fillable = array('uri', 'epsg', 'description');

    /**
     * Relationship with the TabularColumns model.
     */
    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }

    /**
     * Relationship with the Definition model.
     */
    public function definition(){
        return $this->morphOne('Definition', 'source');
    }

    /**
     * Hook into the save function of Eloquent by saving the parent
     * and establishing a relation to the TabularColumns model.
     *
     * Pre-requisite: parameters have already been validated.
     */
    public function save(array $options = array()){

        // Get the columns out of the csv file before saving the csv definition.
        // TODO allow for column aliases to be passed.

        $columns = array();

        $options = array('noparts' => false);
        $is_url = (substr($this->uri , 0, 4) == "http");
        $tmp_dir = sys_get_temp_dir();

        if ($is_url) {

            // This remains untested.
            $tmp_file = uniqid();
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shp", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".shp"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".dbf", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".dbf"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shx", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".shx"));

            // along this file the class will use file.shx and file.dbf
            $shp = new \ShapeFile($tmp_dir . '/' . $tmp_file . ".shp", $options);
        } else {

            // along this file the class will use file.shx and file.dbf
            $shp = new \ShapeFile($this->uri, $options);
        }

        $record = $shp->getNext();

        // read meta data
        if(!$record){
            \App::abort(452, "We failed to retrieve a record from the provided shape file on uri $this->uri, make sure the corresponding dbf and shx files are at the same location.");
        }

        $dbf_fields = $record->getDbfFields();
        $column_index = 0;

        foreach ($dbf_fields as $field) {

            $property = strtolower($field["fieldname"]);
            array_push($columns, array($column_index, $property, $property));
            $column_index++;
        }

        $shp_data = $record->getShpData();

        if(isset($shp_data['parts'])) {
            self::processCoordinates($shp_data['parts'], );
            array_push($columns, array($column_index, 'coords', 'coords'));
        }

        if(isset($shp_data['x'])) {
            // TODO add to geoproperties and link with SHP definition
            array_push($columns, array($column_index, 'lat', 'lat'));
            array_push($columns, array($column_index + 1, 'long', 'long'));
        }

        parent::save();

        foreach($columns as $column){
            $tabular_column = new TabularColumns();
            $tabular_column->index = $column[0];
            $tabular_column->column_name = $column[1];
            $tabular_column->is_pk = 0;
            $tabular_column->column_name_alias = $column[2];
            $tabular_column->tabular_type = 'ShpDefinition';
            $tabular_column->tabular_id = $this->id;
            $tabular_column->save();
        }

        return true;
    }

    /**
     * Validate the input for this model.
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Retrieve the set of create parameters that make up a SHP definition.
     */
    public static function getCreateProperties(){
        return array(
                'uri' => array(
                    'required' => true,
                    'description' => 'The location of the SHP file, either a URL or a local file location.',
                    ),
                'epsg' => array(
                    'required' => false,
                    'description' => 'This parameter holds the EPSG code in which the geometric properties in the shape file are encoded.',
                    'default_value' => 4326
                    ),
                'description' => array(
                    'required' => true,
                    'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                )
            );
    }

    /**
     * Retrieve the set of create parameters that make up a CSV definition.
     * Include the parameters that make up relationships with this model.
     */
    public static function getAllProperties(){
        return self::getCreateProperties();
    }

    /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'uri' => 'required',
            'description' => 'required',
        );
    }

    /**
     * Provide the correct geometric string for a given set of coordinates.
     * Used to provide information in the geoproperties model.
     */
    public static function processCoordinates($coords, $geo_props = array()){



        if(isset($array)) {

            $longkey = self::array_key_exists_nc("long", $array);
            if (!$longkey) {
                $longkey = self::array_key_exists_nc("longitude",$array);
            }
            $latkey = self::array_key_exists_nc("lat",$array);
            if (!$latkey) {
                $latkey = self::array_key_exists_nc("latitude",$array);
            }
            $coordskey = self::array_key_exists_nc("coords",$array);
            if (!$coordskey) {
                $coordskey = self::array_key_exists_nc("coordinates",$array);
            }
            if($longkey && $latkey) {
                $long = $array[$longkey];
                $lat = $array[$latkey];
                unset($array[$longkey]);
                unset($array[$latkey]);
                $name = self::xmlgetelement($array);
                $extendeddata = self::getExtendedDataElement($array);
            } else if($coordskey) {
                $coords = explode(";",$array[$coordskey]);
                unset($array[$coordskey]);
                $name = self::xmlgetelement($array);
                $extendeddata = self::getExtendedDataElement($array);
            }
            else {
                $body .= self::getArray($array);
            }
            if(($lat != "" && $long != "") || count($coords) != 0){
                $body .= "<Placemark><name>". htmlspecialchars($key) ."</name><description>".$name."</description>";
                $body .= $extendeddata;
                if($lat != "" && $long != "") {
                    $body .= "<Point><coordinates>".$long.",".$lat."</coordinates></Point>";
                }
                if (count($coords)  > 0) {
                    if (count($coords) == 1 ) {
                        $all_coords = explode(" ", $coords[0]);

                        if($all_coords[0] == $all_coords[count($all_coords)-1]){
                                // Detected ring
                            $body .= "<Polygon><outerBoundaryIs><LinearRing><coordinates>".$coords[0]."</coordinates></LinearRing></outerBoundaryIs></Polygon>";
                        }else{
                                // Just a multiline
                            $body .= "<LineString><coordinates>".$coords[0]."</coordinates></LineString>";
                        }
                    } else {
                        $body .= "<MultiGeometry>";
                        foreach($coords as $coord) {
                            $body .= "<LineString><coordinates>".$coord."</coordinates></LineString>";
                        }
                        $body .= "</MultiGeometry>";
                    }
                }
                $body .= "</Placemark>";
            }
        }


        foreach($coords as $coord){
            var_dump($coord);
        }

        exit();
    }

    /**
     * Case insensitive version of array_key_exists.
     * Returns the matching key on success, else false.
     *
     * @param string $key
     * @param array $search
     * @return string|false
     */
    private static function array_key_exists_nc($key, $search) {
        if (array_key_exists($key, $search)) {
            return $key;
        }
        if (!(is_string($key) && is_array($search) && count($search))) {
            return false;
        }
        $key = strtolower($key);
        foreach ($search as $k => $v) {
            if (strtolower($k) == $key) {
                return $k;
            }
        }
        return false;
    }
}