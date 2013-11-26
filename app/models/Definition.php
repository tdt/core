<?php

/**
 * Definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Definition extends Eloquent{

    protected $fillable = array('title','subject','description','publisher','contributor','date','type','format','identifier','source','language','relation','coverage','rights');
    /**
     * Return the poly morphic relationship with a source type.
     */
    public function source(){
        return $this->morphTo();
    }

    /**
     * Return the properties ( = column fields ) for this model.
     */
    public static function getCreateParameters(){
        return array(
                'title' => array(
                    'required' => false,
                    'description' => 'A name given to the resource.',
                    'type' => 'string',
                ),
                'subject' => array(
                    'required' => false,
                    'description' => 'The topic of the resource.',
                    'type' => 'string',
                ),
                'description' => array(
                    'required' => false,
                    'description' => 'An account of the resource.',
                    'type' => 'string',
                ),
                'publisher' => array(
                    'required' => false,
                    'description' => 'An entity responsible for making the resource available.',
                    'type' => 'string',
                ),
                'contributor' => array(
                    'required' => false,
                    'description' => 'An entity responsible for making contributions to the resource.',
                    'type' => 'string',
                ),
                'date' => array(
                    'required' => false,
                    'description' => 'A point or period of time associated with an event in the lifecycle of the resource. Best practise is to use the ISO 8601 scheme.',
                    'type' => 'string',
                ),
                'type' => array(
                    'required' => false,
                    'description' => 'The nature or genre of the resource.',
                    'type' => 'string',
                ),
                'format' => array(
                    'required' => false,
                    'description' => 'The file format, physical medium, or dimensions of the resource.',
                    'type' => 'string',
                ),
                'identifier' => array(
                    'required' => false,
                    'description' => 'An unambiguous reference to the resource within a given context.',
                    'type' => 'string',
                ),
                'source' => array(
                    'required' => false,
                    'description' => 'A related resource from which the described resource is derived.',
                    'type' => 'string',
                ),
                'language' => array(
                    'required' => false,
                    'description' => 'A language of the resource.',
                    'type' => 'string',
                ),
                'relation' => array(
                    'required' => false,
                    'description' => 'A related resource.',
                    'type' => 'string',
                ),
                'coverage' => array(
                    'required' => false,
                    'description' => 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant.',
                    'type' => 'string',
                ),
                'rights' => array(
                    'required' => false,
                    'description' => 'Information about rights held in and over the resource.',
                    'type' => 'string',
                ),
        );
    }

    /**
     * Return all properties from a definition, including the properties of his relational objects
     */
    public function getAllParameters(){

        $properties = array();
        $source_definition = $this->source()->first();

        // Add all the properties that are mass assignable
        foreach($source_definition->getFillable() as $key){
            $properties[$key] = $source_definition->getAttributeValue($key);
        }

        // If the source type has a relationship with tabular columns, then attach those to the properties
        if(method_exists(get_class($source_definition), 'tabularColumns')){

            $columns = $source_definition->tabularColumns();
            $columns = $columns->getResults();

            $columns_props = array();
            foreach($columns as $column){
                array_push($columns_props, array(
                    'column_name' => $column->column_name,
                    'is_pk' => $column->is_pk,
                    'column_name_alias' => $column->column_name_alias,
                    'index' => $column->index,
                ));
            }

            $properties['columns'] = $columns_props;
        }

        // If the source type has a relationship with geoproperties, attach those to the properties
        if(method_exists(get_class($source_definition), 'geoProperties')){

            $geo_props = $source_definition->geoProperties();
            $geo_props = $geo_props->getResults();

            $geo_props_arr = array();
            foreach($geo_props as $geo_prop){

                $geo_entry = new \stdClass();

                $geo_entry->path = $geo_prop->path;
                $geo_entry->property = $geo_prop->property;
                array_push($geo_props_arr, $geo_entry);
            }

            $properties['geo'] = $geo_props_arr;
        }

        return $properties;
    }

    /**
     * Delete the related source type
     */
    public function delete(){

        $source_type = $this->source()->first();
        $source_type->delete();

        parent::delete();
    }
}
