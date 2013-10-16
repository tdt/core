<?php

/**
 * CSV definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class CsvDefinition extends SourceType{

    protected $table = 'csvdefinitions';

    protected $guarded = array('id');

    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
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

        if (($handle = fopen($this->uri, "r")) !== FALSE) {

            // for further processing we need to process the header row, this MUST be after the comments
            // so we're going to throw away those lines before we're processing our header_row
            // our first line will be processed due to lazy evaluation, if the start_row is the first one
            // then the first argument will return false, and being an &&-statement the second validation will not be processed
            $commentlinecounter = 1;
            while ($commentlinecounter < $this->start_row) {
                $line = fgetcsv($handle, 0, $this->delimiter, '"');
                $commentlinecounter++;
            }

            $index = 0;

            if (($line = fgetcsv($handle, 0, $this->delimiter, '"')) !== FALSE) {

                $index++;

                for ($i = 0; $i < sizeof($line); $i++) {

                    // TODO set the alias column last in the array.
                    array_push($columns, array($i, trim($line[$i]), trim($line[$i])));
                }
            }else{
                \App::abort(452, "The columns could not be retrieved from the csv file on location $uri.");
            }
            fclose($handle);
        } else {
            \App::abort(452, "The columns could not be retrieved from the csv file on location $uri.");
        }

        // If the columns were parsed correctly, save this definition and use the id to link them to the column objects.
        parent::save();
        if(empty($this->id)){
            \App::abort(452, "The csv definition could not be saved after validation, check if the provided properties are still correct.");
        }

        foreach($columns as $column){
            $tabular_column = new TabularColumns();
            $tabular_column->index = $column[0];
            $tabular_column->column_name = $column[1];
            $tabular_column->is_pk = 0;
            $tabular_column->column_name_alias = $column[2];
            $tabular_column->tabular_type = 'CsvDefinition';
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
     * Retrieve the set of create parameters that make up a CSV definition.
     */
    public static function getCreateParameters(){

        return array(
            'uri' => array(
                'required' => true,
                'description' => 'The location of the CSV file, either a URL or a local file location.',
            ),
            'delimiter' => array(
                'required' => false,
                'description' => 'The delimiter of the separated value file.',
                'default_value' => ',',
            ),
            'has_header_row' => array(
                'required' => false,
                'description' => 'Boolean parameter defining if the separated value file contains a header row that contains the column names.',
                'default_value' => 1,
            ),
            'start_row' => array(
                'required' => false,
                'description' => 'Defines the row at which the data (and header row if present) starts in the file.',
                'default_value' => 1,
            ),
            'documentation' => array(
                'required' => true,
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
            )
        );
    }

    /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'has_header_row' => 'integer|min:0|max:1',
            'start_row' => 'integer',
            'uri' => 'uri',
        );
    }
}