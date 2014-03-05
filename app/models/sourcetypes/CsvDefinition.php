<?php

/**
 * CSV definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class CsvDefinition extends SourceType{

    protected $table = 'csvdefinitions';

    protected $fillable = array('uri', 'delimiter', 'has_header_row', 'start_row', 'description');

    /**
     * Relationship with the TabularColumns model.
     */
    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }

    /**
     * Relationship with the Geo properties model.
     */
    public function geoProperties(){
        return $this->morphMany('GeoProperty', 'source');
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

            return -1;

        }

        return parent::__get($name);
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

        return parent::delete();
    }
}
