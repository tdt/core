<?php

namespace repositories;

use Definition;
use repositories\interfaces\DefinitionRepositoryInterface;
use repositories\BaseRepository;

class DefinitionRepository extends BaseRepository implements DefinitionRepositoryInterface{

    protected $rules = array(
        'resource_name' => 'required',
        'collection_uri' => 'required|collectionuri'
    );

    public function __construct(\Definition $model){
        $this->model = $model;
    }

    /**
     * Create a new definition with corresponding source type
     */
    public function store($input){

        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        // Validate the source type input
        $this->validateType($input);

        // Delete the identifier (PUT = overwrite)
        $this->delete($input['collection_uri'] . '/' . $input['resource_name']);

        $type = $input['type'];

        // Use the power of the IoC
        $source_repository = \App::make('repositories\interfaces\\' . ucfirst($type) . 'DefinitionRepositoryInterface');

        // Create the new source type
        $source = $source_repository->store($input);

        // Create the new definition
        $definition = $this->model->create(array(
            'resource_name' => $input['resource_name'],
            'collection_uri' => $input['collection_uri'],
            'source_id' => $source['id'],
            'source_type' => ucfirst(strtolower($source['type'])) . 'Definition',
        ));

        // Add the rest of the properties
        foreach(array_only($input, array_keys($this->getCreateParameters())) as $property => $value){
            $definition->$property = $value;
        }

        $definition->save();

        return $definition->toArray();
    }

    public function update($identifier, $input){

        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $definition = $this->getByIdentifier($identifier);

        if(empty($definition)){
            \App::abort(404, "The resource with identifier '$identifier' could not be found.");
        }

        $source_type = $definition->source()->first();
        $source_repository = \App::make('repositories\interfaces\\' . ucfirst(strtolower($source_type->type)) . 'DefinitionRepositoryInterface');

        $this->validateType($input);

        $source_repository->update($source_type->id, $input);

        $definition->update(array_only($input, array_keys($this->getCreateParameters())));

        return $definition->toArray();
    }

    /**
     * Delete a definition
     */
    public function delete($identifier){

        $definition = $this->getByIdentifier($identifier);

        if(!empty($definition))
            $definition->delete();
    }

    /**
     * Return true|false based on whether the identifier is
     * a resource or not
     */
    public function exists($identifier){

        $definition = $this->getByIdentifier($identifier);

        return !empty($definition);
    }


    public function getAll($limit = PHP_INT_MAX, $offset = 0){
        return \Definition::take($limit)->skip($offset)->get();
    }

    public function getAllPublished($limit = PHP_INT_MAX, $offset = 0){
        return \Definition::where('draft', '=', 0)->take($limit)->skip($offset)->get();
    }


    public function getByIdentifier($identifier){
        return \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($identifier . '/'))->first();
    }


    public function getByCollection($collection){
        return \Definition::whereRaw("CONCAT(collection_uri, '/') like CONCAT(?, '%')", array($uri . '/'))->get();
    }


    public function getOldest(){

    }

    public function count(){
        return \Definition::all()->count();
    }

    /**
     * Return the count of all non-draft definitions
     */
    public function countPublished(){
        return \Definition::where('draft', '=', 0)->count();
    }

    /**
     * Check if the given source type exists
     */
    private function validateType($input){

        $type = @$input['type'];

        // Use the power of the IoC
        $source_repository = \App::make('repositories\\interfaces\\' . ucfirst($type) . 'DefinitionRepositoryInterface');

        $validator = $source_repository->getValidator($input);

        if($validator->fails()){
            $message = $validator->messages()->first();
            \App::abort(400, "Something went wrong during validation, the message we got is: " . $message);
        }
    }

    private function getVisible(){
        return array('source_id', 'source_type', 'title','description','date','type','format','source','language','rights', 'cache_minutes', 'draft');
    }

    /**
     * Return the properties ( = column fields ) for this model.
     */
    public function getCreateParameters(){

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
                'draft' => array(
                    'required' => false,
                    'name' => 'Draft',
                    'type' => 'boolean',
                    'description' => 'Draft definitions are not shown to the public when created, however the URI space they take is reserved.',
                ),
        );
    }

    public function getAllFullDescriptions($uri, $limit, $offset){

        $definitions = array();

        foreach($this->getAll($limit, $offset) as $definition){

            $identifier = $definition->collection_uri . '/' . $definition->resource_name;
            $definitions[$identifier] = $this->getFullDescription($identifier);
        }

        return $definitions;
    }

    public function getFullDescription($uri){

        $definition = $this->getByIdentifier($uri);

        $properties = array();
        $source_definition = $definition->source()->first();

        foreach($this->getVisible() as $key){
            $properties[$key] = $definition->$key;
        }

        // Add all the properties that are mass assignable
        foreach($source_definition->getFillable() as $key){
            $properties[$key] = $source_definition->$key;
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

        $properties['type'] = strtolower($source_definition->type);

        return $properties;
    }

    /**
     * Return all properties from a definition, including the properties of his relational objects
     */
    /*public function getAllParameters(){

        $properties = array();
        $source_definition = $definition->source()->first();

        foreach($definition->getFillable() as $key){
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

        $properties['type'] = strtolower($source_definition->type);

        return $properties;
    }*/

}