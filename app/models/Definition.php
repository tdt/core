<?php

/**
 * Definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Definition extends Eloquent{

    protected $fillable = array('title','description','date','type','format','source','language','rights', 'cache_minutes');
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
                    'name' => 'Title',
                    'description' => 'A name given to the resource.',
                    'type' => 'string',
                    'group' => 'dc',
                ),
                'date' => array(
                    'required' => false,
                    'name' => 'Date',
                    'description' => 'A point or period of time associated with an event in the lifecycle of the resource. Best practise is to use the ISO 8601 scheme.',
                    'type' => 'string',
                    'group' => 'dc',
                ),
                'source' => array(
                    'required' => false,
                    'name' => 'Source',
                    'description' => 'A related resource from which the described resource is derived.',
                    'type' => 'string',
                    'group' => 'dc',
                ),
                'language' => array(
                    'required' => false,
                    'name' => 'Language',
                    'description' => 'A language of the resource.',
                    'type' => 'list',
                    'list' => 'api/languages',
                    'list_option' => 'name',
                    'group' => 'dc',
                ),
                'rights' => array(
                    'required' => false,
                    'name' => 'Rights',
                    'type' => 'list',
                    'list' => 'api/licenses',
                    'list_option' => 'title',
                    'description' => 'Information about rights held in and over the resource.',
                    'group' => 'dc',
                ),
                'cache_minutes' => array(
                    'required' => false,
                    'name' => 'Cache',
                    'type' => 'integer',
                    'description' => 'How long this resource should be cached (in minutes).',
                ),
        );
    }

    /**
     * Return all properties from a definition, including the properties of his relational objects
     */
    public function getAllParameters(){

        $properties = array();
        $source_definition = $this->source()->first();

        foreach($this->getFillable() as $key){
            $properties[$key] = $this->getAttributeValue($key);
        }

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

        $properties['type'] = strtolower($source_definition->getType());

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
