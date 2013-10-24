<?php

/**
 * Definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Definition extends Eloquent{

    protected $guarded = array('id', 'source_id');

    /**
     * Return the poly morphic relationship with a source type.
     */
    public function source(){
        return $this->morphTo();
    }

    /**
     * Return the properties ( = column fields ) for this model.
     */
    public static function getCreateProperties(){
        return array(
                'title' => array(
                    'required' => true,
                    'description' => 'A name given to the resource.',
                ),
                'subject' => array(
                    'required' => false,
                    'description' => 'The topic of the resource.',
                ),
                'description' => array(
                    'required' => false,
                    'description' => 'An account of the resource.',
                ),
                'publisher' => array(
                    'required' => false,
                    'description' => 'An entity responsible for making the resource available.',
                ),
                'contributor' => array(
                    'required' => true,
                    'description' => 'An entity responsible for making contributions to the resource.',
                ),
                'date' => array(
                    'required' => true,
                    'description' => 'A point or period of time associated with an event in the lifecycle of the resource.',
                ),
                'type' => array(
                    'required' => true,
                    'description' => 'The nature or genre of the resource.',
                ),
                'format' => array(
                    'required' => true,
                    'description' => 'The file format, physical medium, or dimensions of the resource.',
                ),
                'identifier' => array(
                    'required' => true,
                    'description' => 'An unambiguous reference to the resource within a given context.',
                ),
                'source' => array(
                    'required' => true,
                    'description' => 'A related resource from which the described resource is derived.',
                ),
                'language' => array(
                    'required' => true,
                    'description' => 'A language of the resource.',
                ),
                'relation' => array(
                    'required' => true,
                    'description' => 'A related resource.',
                ),
                'coverage' => array(
                    'required' => true,
                    'description' => 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant.',
                ),
                'rights' => array(
                    'required' => true,
                    'description' => 'Information about rights held in and over the resource.',
                ),
        );
    }

    /**
     * Return all properties from a definition, including the properties of his relational objects
     */
    public function getAllProperties(){

        $properties = array();
        $source_definition = $this->source()->first();

        // Add all the properties that are mass assignable.
        foreach($source_definition->getFillable() as $key){
            $properties[$key] = $source_definition->getAttributeValue($key);
        }

        // If the source type has a relationship with tabular columns, then attach those to the properties.
        if(method_exists(get_class($source_definition), 'tabularColumns')){

            $columns = $source_definition->tabularColumns();
            $columns = $columns->getResults();

            $columns_props = array();
            foreach($columns as $column){
                $columns_props[$column->index] = array(
                    'column_name' => $column->column_name,
                    'is_pk' => $column->is_pk,
                    'column_name_alias' => $column->column_name_alias,
                );
            }

            $properties['columns'] = $columns_props;
        }

        return $properties;
    }
}
