<?php

use tdt\core\datacontrollers\XLSController;

/**
 * Excell definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XlsDefinition extends SourceType{

    protected $table = 'xlsdefinitions';

    protected $fillable = array('uri', 'sheet', 'has_header_row', 'start_row', 'description');

    /**
     * Relationship with the TabularColumns model.
     */
    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }

     /**
     * Validate the input for this model.
     */
    public static function validate($params){

        $tabular_params = @$params['columns'];
        TabularColumns::validate($tabular_params);

        return parent::validate($params);
    }

    /**
     * Hook into the save function of Eloquent by saving the parent
     * and establishing a relation to the TabularColumns model.
     *
     * Pre-requisite: parameters have already been validated.
     */
    public function save(array $options = array()){

        // Check for passed columns
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

        // Unset the pk parameter, serves as a shortcut for the columns configuration
        unset($this->pk);

        parent::save();

        foreach($columns as $column){

            // Shortcut to define the primary key is to pass the index with the definition
            // instead of passing it with the columns-part of the definition
            $is_pk = false;

            $column['is_pk'] = false;

            if(isset($options['pk']) && $column['index'] == $options['pk']){
                $column['is_pk'] = true;
            }

            $tabular_column = new TabularColumns();
            $tabular_column->index = $column['index'];
            $tabular_column->column_name = $column['column_name'];
            $tabular_column->is_pk = $column['is_pk'];
            $tabular_column->column_name_alias = $column['column_name_alias'];
            $tabular_column->tabular_type = 'XlsDefinition';
            $tabular_column->tabular_id = $this->id;
            $tabular_column->save();
        }

        return true;
    }

    /**
     * Update the XlsDefinition model
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

        // If columns are passed, they'll be present in the 'all'
        $params['columns'] = @$attr['all']['columns'];
        $params['pk'] = @$attr['all']['pk'];

        $this->save($params);
    }

    /**
     * Retrieve the set of create parameters that make up a XLS definition.
     */
    public static function getCreateParameters(){

        return array(
                'uri' => array(
                    'required' => true,
                    'name' => 'URI',
                    'description' => 'The location of the XLS file, either a URL or a local file location.',
                    'type' => 'string',
                ),
                'description' => array(
                    'required' => true,
                    'name' => 'Description',
                    'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                    'type' => 'string',
                ),
                'sheet' => array(
                    'required' => false,
                    'name' => 'XLS sheet',
                    'description' => 'The sheet name in which the tabular data resides.',
                    'default_value' => ',',
                    'type' => 'string',
                ),
                'has_header_row' => array(
                    'required' => false,
                    'name' => 'Header row',
                    'description' => 'Boolean parameter defining if the XLS file contains a header row that contains the column names.',
                    'default_value' => 1,
                    'type' => 'boolean',
                ),
                'start_row' => array(
                    'required' => false,
                    'name' => 'Start row',
                    'description' => 'Defines the row at which the data (and header row if present) starts in the file.',
                    'default_value' => 0,
                    'type' => 'integer',
                ),
                'pk' => array(
                    'required' => false,
                    'name' => 'Primary key',
                    'description' => 'This is a shortcut to define a primary key of this dataset. The value must be the index of the column you want each row to be mapped on. The pk property will never explicitly appear in the definition, but will manifest itself as part of a column property.',
                    'type' => 'integer',
                ),
        );
    }

    /**
     * Overwrite the magic __get function to retrieve the primary key
     * parameter. This isn't a real parameter but a derived one from the tabularcolumns
     * relation.
     */
    public function __get($name){

        if($name == 'pk'){

            // Retrieve the primary key from the columns
            // Get the related columns
            $columns = $this->tabularColumns()->getResults();

            foreach($columns as $column){
                if($column->is_pk){
                    return $column->index;
                }
            }

            return null;

        }

        return parent::__get($name);
    }

     /**
     * Retrieve the set of create parameters that make up a XLS definition.
     * Include the parameters that make up relationships with this model.
     */
    public static function getAllParameters(){

         $column_params = array(
            'columns' =>
                array('description' => 'Columns must be an array of objects of which the template is described in the parameters section.',
                  'parameters' => TabularColumns::getCreateParameters(),
            ),
        );

        return array_merge(self::getCreateParameters(), $column_params);
    }

    /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'has_header_row' => 'integer|min:0|max:1',
            'start_row' => 'integer',
            'uri' => 'uri|required',
            'description' => 'required',
        );
    }

    /**
     * Get the file extension from the file name.
     */
    private function getFileExtension($file){
        return strtolower(substr(strrchr($file, '.'), 1));
    }

    /**
     * Retrieve colummn information from the request parameters.
     */
    private function parseColumns($options){

        $aliases = @$options['columns'];
        $pk = @$options['pk'];

        if(empty($aliases)){
            $aliases = array();
        }


        $columns = array();
        $tmp_dir = sys_get_temp_dir();

        if(empty($columns)){

            if (!is_dir($tmp_dir)) {
                mkdir($tmp_dir);
            }

            $is_uri = (substr($this->uri , 0, 4) == "http");

            try{
                if ($is_uri) {
                $tmp_file = uniqid();

                    file_put_contents($tmp_dir. "/" . $tmp_file, file_get_contents($this->uri));
                    $php_obj = XLSController::loadExcel($tmp_dir ."/" . $tmp_file, $this->getFileExtension($this->uri), $this->sheet);
                } else {
                    $php_obj = XLSController::loadExcel($this->uri, $this->getFileExtension($this->uri),$this->sheet);
                }

                $worksheet = $php_obj->getSheetByName($this->sheet);

            }catch(Exception $ex){
                \App::abort(404, "Something went wrong whilst retrieving the Excel file from uri $this->uri.");
            }


            if(is_null($worksheet)){
                \App::abort(404, "The sheet with name, $this->sheet, has not been found in the Excel file.");
            }

            foreach ($worksheet->getRowIterator() as $row) {

                $row_index = $row->getRowIndex();

                // Rows start at 1 in XLS
                if ($row_index == $this->start_row + 1) {

                    $cell_iterator = $row->getCellIterator();
                    $cell_iterator->setIterateOnlyExistingCells(false);

                    $column_index = 0;

                    foreach($cell_iterator as $cell){

                        if($cell->getCalculatedValue() != ""){

                            $cell_value = trim($cell->getCalculatedValue());

                            // Try to get an alias from the options, if it's empty
                            // then just take the column value as alias
                            $alias = @$aliases[$column_index];

                            if(empty($alias)){
                                $alias = $cell_value;
                            }

                            array_push($columns, array('index' => $column_index, 'column_name' => $cell->getCalculatedValue(), 'column_name_alias' => $alias, 'is_pk' => ($pk === $column_index)));
                        }
                        $column_index++;
                    }

                    break;
                }
            }

            $php_obj->disconnectWorksheets();

            if ($is_uri) {
                unlink($tmp_dir . "/" . $tmp_file);
            }
        }

        return $columns;
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

        parent::delete();
    }

}
