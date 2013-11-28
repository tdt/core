<?php


// PHP SHP libraries arent PSR-0 yet so we have to include them
include_once(__DIR__ . "/../../lib/ShapeFile.inc.php");
include_once(__DIR__ . "/../../lib/proj4php/proj4php.php");

/**
 * Shape definition model, all processing is done based on the
 * SHP specification http://www.esri.com/library/whitepapers/pdfs/shapefile.pdf.
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
     * Relationship with the Geo properties model.
     * this will probably break the relationship or displaying of the data.
     * If so every line or entry needs to have a geo property, or has to be parsed at runtime.
     */
    public function geoProperties(){
        return $this->morphMany('GeoProperty', 'source');
    }

    /**
     * Hook into the save function of Eloquent by saving the parent
     * and establishing a relation to the TabularColumns model.
     *
     * Pre-requisite: parameters have already been validated.
     */
    public function save(array $options = array()){

        // If geo properties are passed, then utilize them
        // If they're not parse the SHP file in to search for them automatically
        $provided_columns = @$options['columns'];

        $columns = $this->parseColumns($options);

         // If columns are provided, check if they exist and have the correct index
        if(!empty($provided_columns)){

            // Validate the provided columns
            TabularColumns::validate($provided_columns);
            $tmp = array();

            // Index the column objects on the column name
            foreach($provided_columns as $column){
                $tmp[$column['column_name']] = $column;
            }

            $tmp_columns = array();
            foreach($columns as $column){
                $tmp_columns[$column['column_name']] = $column;
            }

            // If the column name of a provided column doesn't exist, or an index doesn't match, abort
            foreach($tmp as $column_name => $column){

                $tmp_column = $tmp_columns[$column_name];
                if(empty($tmp_column)){
                    \App::abort(404, "The column name ($column_name) was not found in the CSV file.");
                }

                if($tmp_column['index'] != $column['index']){
                    \App::abort(400, "The column name ($column_name) was found, but the index isn't correct.");
                }
            }

            // Everything went well, columns are now the provided columns by the user
            $columns = $provided_columns;
        }

        // Keep track of the column name aliases
        $column_aliases = array();

        foreach($columns as $column){
            array_push($column_aliases, $column['column_name_alias']);
        }

        // Check if geo properties are given by the user
        $user_geo_properties = @$options['geo'];


        $parsed_geo_properties = $this->parseGeoProperty($columns);

        parent::save();

        // Save the TabularColumns
        foreach($columns as $column){

            $tabular_column = new TabularColumns();
            $tabular_column->index = $column['index'];
            $tabular_column->column_name = $column['column_name'];
            $tabular_column->is_pk = $column['is_pk'];
            $tabular_column->column_name_alias = $column['column_name_alias'];
            $tabular_column->tabular_type = 'ShpDefinition';
            $tabular_column->tabular_id = $this->id;
            $tabular_column->save();
        }

        // Delete current geo properties
        $geo_properties = $this->geoProperties;

        if(!empty($geo_properties)){
            foreach($geo_properties as $geo_prop){
                $geo_prop->delete();
            }
        }

        // If geo properties are given with the request, check if they're valid
        if(!empty($user_geo_properties)){

            // Index the parsed geo properties on their column name for validation purposes
            $tmp = array();
            foreach($parsed_geo_properties as $parsed_prop){
                $tmp[$parsed_prop['path']] = $parsed_prop;
            }

            foreach($user_geo_properties as $geo_property){

                $path = $geo_property['path'];
                if(!in_array($path, $column_aliases)){

                    \App::abort(404, "Can't find the column $path in the binary shape structure");
                }

                // It could be that the given property isn't valid, if so, abort
                $geo = $tmp[$geo_property['path']];
                if($geo_property['property'] != $geo['property']){
                    \App::abort(400, "The column, $path, was found but the property didn't match the one we found in the shape file.");
                }
            }

            $parsed_geo_properties = $user_geo_properties;
        }

        // Save the GeoProperty
        foreach($parsed_geo_properties as $geo_prop){

            $geo_property = new GeoProperty();
            $geo_property->path = $geo_prop['path'];;
            $geo_property->property = $geo_prop['property'];
            $geo_property->source_id = $this->id;
            $geo_property->source_type = 'ShpDefinition';
            $geo_property->save();
        }

        return true;
    }

    /**
     * Update the CsvDefinition model
     */
    public function update(array $attr = array()){

        // When a new property is given for the CsvDefinition model
        // revalidate the entire definition, including columns.
        $columns = $this->tabularColumns()->getResults();

        foreach($columns as $column){
            $column->delete();
        }

        $parameters = $attr['source'];
        foreach($parameters as $key => $value){
            $this->$key = $value;
        }

        // If columns or geo, etc. are passed, they'll be present in the 'all'

        $params['columns'] = @$attr['all']['columns'];
        $params['geo'] = @$attr['all']['geo'];

        $this->save($params);
    }


    /**
     * Parse the column names out of a SHP file.
     *
     * TODO clean up this function a bit.
     */
    private function parseColumns($options){

        $options = array('noparts' => false);
        $is_url = (substr($this->uri , 0, 4) == "http");
        $tmp_dir = sys_get_temp_dir();
        $columns = array();

        $pk = @$options['pk'];

        if ($is_url) {

            // This remains untested
            $tmp_file = uniqid();
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shp", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".shp"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".dbf", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".dbf"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shx", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".shx"));

            // Along this file the class will use file.shx and file.dbf
            $shp = new \ShapeFile($tmp_dir . '/' . $tmp_file . ".shp", $options);
        } else {

            // along this file the class will use file.shx and file.dbf
            $shp = new \ShapeFile($this->uri, $options);
        }

        $record = $shp->getNext();

        // Read meta data
        if(!$record){
            \App::abort(400, "We failed to retrieve a record from the provided shape file on uri $this->uri, make sure the corresponding dbf and shx files are at the same location.");
        }

        // Get the dBASE fields
        $dbf_fields = $record->getDbfFields();
        $column_index = 0;

        foreach ($dbf_fields as $field) {

            $property = strtolower($field["fieldname"]);
            array_push($columns, array('index' => $column_index, 'column_name' => $property, 'column_name_alias' => $property, 'is_pk' => ($pk === $column_index)));
            $column_index++;
        }

        $shp_data = $record->getShpData();

        // Get the geographical column names
        // Either coords will be set (identified by the parts)
        // or a lat long will be set (identified by x and y)
        if(!empty($shp_data['parts'])) {
            array_push($columns, array('index' => $column_index, 'column_name' => 'parts', 'column_name_alias' => 'parts', 'is_pk' => 0));
        }else if(!empty($shp_data['x'])) {
            array_push($columns, array('index' => $column_index, 'column_name' => 'x', 'column_name_alias' => 'x', 'is_pk' => 0));
            array_push($columns, array('index' => $column_index + 1, 'column_name' => 'y', 'column_name_alias' => 'y', 'is_pk' => 0));
        }else{
            \App::abort(400, 'The shapefile could not be processed, probably because the geometry in the shape file is not supported.
                The supported geometries are Null Shape, Point, PolyLine, Polygon and MultiPoint');
        }

        return $columns;
    }


    /**
     * Parse the geo column names out of a SHP file.
     */
    private function parseGeoProperty($columns){

        // Make sure the geo property's path is mapped onto the column alias
        $aliases = array();
        foreach($columns as $column){
            $aliases[$column['column_name']] = $column['column_name_alias'];
        }

        $options = array('noparts' => false);
        $is_url = (substr($this->uri , 0, 4) == "http");
        $tmp_dir = sys_get_temp_dir();
        $geo_properties = array();

        if ($is_url) {

            // This remains untested
            $tmp_file = uniqid();
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shp", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".shp"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".dbf", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".dbf"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shx", file_get_contents(substr($this->uri, 0, strlen($this->uri) - 4) . ".shx"));

            $shp = new \ShapeFile($tmp_dir . '/' . $tmp_file . ".shp", $options);
        } else {
            $shp = new \ShapeFile($this->uri, $options);
        }

        $record = $shp->getNext();

        // read meta data
        if(!$record){
            \App::abort(400, "We failed to retrieve a record from the provided shape file on uri $this->uri, make sure the corresponding dbf and shx files are at the same location.");
        }

        $shp_data = $record->getShpData();
        $shape_type = strtolower($record->getRecordClass());

        $geo_properties = array();

        // Get the geographical column names
        // Either multiple coordinates will be set (identified by the parts)
        // or a lat long pair will be set (identified by x and y)
        if(!empty($shp_data['parts'])) {
            if(strpos($shape_type, 'polyline')){
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polyline', 'path' => $parts));
            }else if(strpos($shape_type, 'polygon')){
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polygon', 'path' => $parts));
            }else{ // TODO support more types
                \App::abort(400, 'Provided geometric type ( $shape_type ) is not supported');
            }
        }else if(isset($shp_data['x'])){
            $x = $aliases['x'];
            $y = $aliases['y'];
            array_push($geo_properties, array('property' => 'latitude', 'path' => $x));
            array_push($geo_properties, array('property' => 'longitude', 'path' => $y));
        }

        return $geo_properties;
    }

    /**
     * Validate the input for this model.
     */
    public static function validate($params){

        $tabular_params = @$params['columns'];
        TabularColumns::validate($tabular_params);

        $geo_params = @$params['geo'];
        GeoProperty::validate($geo_params);

        return parent::validate($params);
    }

    /**
     * Retrieve the set of create parameters that make up a SHP definition.
     */
    public static function getCreateParameters(){
        return array(
                'uri' => array(
                    'required' => true,
                    'description' => 'The location of the SHP file, either a URL or a local file location.',
                    'type' => 'string',
                    ),
                'epsg' => array(
                    'required' => false,
                    'description' => 'This parameter holds the EPSG code in which the geometric properties in the shape file are encoded.',
                    'default_value' => 4326,
                    'type' => 'string',
                    ),
                'description' => array(
                    'required' => true,
                    'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                    'type' => 'string',
                )
            );
    }

    /**
     * Retrieve the set of create parameters that make up a CSV definition.
     * Include the parameters that make up relationships with this model.
     */
    public static function getAllParameters(){

         $column_params = array('columns' => array('description' => 'Columns must be an array of objects of which the template is described in the parameters section.',
                                                'parameters' => TabularColumns::getCreateParameters(),
                                            )
        );

        $geo_params = array('geo' => array('description' => 'Geo must be an array of objects of which the template is described in the parameters section.',
                                            'parameters' => GeoProperty::getCreateParameters(),
        ));

        return array_merge(self::getCreateParameters(), $column_params, $geo_params);
    }

    /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'uri' => 'required|uri',
            'description' => 'required',
        );
    }

     /**
     * Because we have related models, and non hard defined foreign key relationships
     * we have to delete our related models ourselves.
     */
    public function delete(){

         // Get the related columns
        $columns = $this->tabularColumns()->getResults();

        foreach($columns as $column){
            $column->delete();
        }

        // Get the related geo properties
        $geo_properties = $this->geoProperties()->getResults();

        foreach($geo_properties as $geo_property){
            $geo_property->delete();
        }

        parent::delete();
    }
}
