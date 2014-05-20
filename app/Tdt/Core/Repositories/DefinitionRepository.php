<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;

class DefinitionRepository extends BaseDefinitionRepository implements DefinitionRepositoryInterface
{

    protected $rules = array(
        'resource_name' => 'required',
        'collection_uri' => 'required|collectionuri'
    );

    public function __construct(\Definition $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new definition with corresponding source type
     */
    public function store(array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        // Validate the source type input
        $this->validateType($input);

        // Delete the identifier (PUT = overwrite)
        $this->delete($input['collection_uri'] . '/' . $input['resource_name']);

        $type = ucfirst(strtolower($input['type']));

        // Use the power of the IoC
        $source_repository = $this->getSourceRepository($type);

        // Create the new source type
        $source = $source_repository->store($input);

        // Create the new definition
        $definition = $this->model->create(array());

        $definition->source_id = $source['id'];
        $definition->source_type = ucfirst(strtolower($source['type'])) . 'Definition';
        $definition->resource_name = $input['resource_name'];
        $definition->collection_uri = $input['collection_uri'];

        // Add the rest of the properties
        foreach (array_only($input, array_keys($this->getCreateParameters())) as $property => $value) {
            $definition->$property = $value;
        }

        $definition->save();

        return $definition->toArray();
    }

    public function update($identifier, array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $definition = $this->getByIdentifier($identifier);

        $source = $this->getDefinitionSource($definition['source_id'], $definition['source_type']);

        $type = ucfirst(strtolower($source['type']));

        if (empty($definition)) {
            \App::abort(404, "The resource with identifier '$identifier' could not be found.");
        }

        $source_repository = $this->getSourceRepository($type);

        $this->validateType($input);

        $source_repository->update($source['id'], $input);

        $definition_object = \Definition::find($definition['id']);

        $definition_object->update(array_only($input, array_keys($this->getCreateParameters())));

        return $definition_object->toArray();
    }

    /**
     * Delete a definition
     */
    public function delete($identifier)
    {
        $definition = $this->getEloquentDefinition($identifier);

        if (!empty($definition)) {
            return $definition->delete();
        }
    }

    /**
     * Return true|false based on whether the identifier is
     * a resource or not
     */
    public function exists($identifier)
    {
        $definition = $this->getByIdentifier($identifier);

        return !empty($definition);
    }


    public function getAll($limit = PHP_INT_MAX, $offset = 0)
    {
        return \Definition::take($limit)->skip($offset)->get()->toArray();
    }

    public function getAllPublished($limit = PHP_INT_MAX, $offset = 0)
    {
        return \Definition::where('draft', '=', 0)->take($limit)->skip($offset)->get()->toArray();
    }


    public function getByIdentifier($identifier)
    {
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($identifier . '/'))->first();

        if (empty($definition)) {
            return array();
        }

        return $definition->toArray();
    }


    public function getByCollection($collection)
    {
        return \Definition::whereRaw("CONCAT(collection_uri, '/') like CONCAT(?, '%')", array($collection . '/'))->get()->toArray();
    }

    public function getOldest()
    {
        $definition = \Definition::where('created_at', '=', \DB::table('definitions')->max('created_at'))->first();

        if (!empty($definition)) {
            return $definition->toArray();
        }

        return $definition;
    }

    public function count()
    {
        return \Definition::all()->count();
    }

    /**
     * Return the count of all non-draft definitions
     */
    public function countPublished()
    {
        return \Definition::where('draft', '=', 0)->count();
    }

    public function getDefinitionSource($id, $name)
    {
        $repository = \App::make('Tdt\\Core\\Repositories\\Interfaces\\' . $name . 'RepositoryInterface');

        return $repository->getById($id);
    }

    /**
     * Check if the given source type exists
     */
    private function validateType(array $input)
    {
        $type = @$input['type'];

        // Use the power of the IoC
        try {
            $type = ucfirst(strtolower($type));
            $source_repository = $this->getSourceRepository($type);
        } catch (\ReflectionException $ex) {
            \App::abort(400, "The provided source type " . $type . " is not supported.");
        }

        $validator = $source_repository->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, "Something went wrong during validation, the message we got is: " . $message);
        }
    }

    public function getAllFullDescriptions($limit = PHP_INT_MAX, $offset = 0)
    {
        $definitions = array();

        foreach ($this->getAll($limit, $offset) as $definition) {

            $identifier = $definition['collection_uri'] . '/' . $definition['resource_name'];
            $definitions[$identifier] = $this->getFullDescription($identifier);
        }

        return $definitions;
    }

    public function getAllDefinitionInfo($limit, $offset)
    {
        $definitions = array();

        foreach ($this->getAll($limit, $offset) as $definition) {

            $identifier = $definition['collection_uri'] . '/' . $definition['resource_name'];
            $definitions[$identifier] = $this->getDescriptionInfo($identifier);
        }

        return $definitions;
    }

    public function getDescriptionInfo($identifier)
    {
        $definition = $this->getEloquentDefinition($identifier);

        $properties = array();
        $source_definition = $definition->source()->first();

        foreach ($definition->getFillable() as $key) {
            $properties[$key] = $definition->$key;
        }

        $properties['type'] = strtolower($source_definition->type);

        return $properties;
    }

    /**
     * This function solves the issues of retrieving relationships of a relationship (e.g. definition -> csvdefinitions -> tabular)
     */
    private function getEloquentDefinition($identifier)
    {
        return \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($identifier . '/'))->first();
    }

    public function getFullDescription($identifier)
    {
        $definition = $this->getEloquentDefinition($identifier);

        $properties = array();
        $source_definition = $definition->source()->first();

        foreach ($definition->getFillable() as $key) {
            $properties[$key] = $definition->$key;
        }

        // Add all the properties that are mass assignable
        foreach ($source_definition->getFillable() as $key) {
            $properties[$key] = $source_definition->$key;
        }

        // If the source type has a relationship with tabular columns, then attach those to the properties
        if (method_exists(get_class($source_definition), 'tabularColumns')) {

            $columns = $source_definition->tabularColumns();
            $columns = $columns->getResults();

            $columns_props = array();
            foreach ($columns as $column) {
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
        if (method_exists(get_class($source_definition), 'geoProperties')) {

            $geo_props = $source_definition->geoProperties();
            $geo_props = $geo_props->getResults();

            $geo_props_arr = array();
            foreach ($geo_props as $geo_prop) {

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
     * Provide a source type repository
     *
     * @param string $type
     * @return mixed
     */
    private function getSourceRepository($type)
    {
        try {
            return \App::make('Tdt\\Core\\Repositories\\Interfaces\\' . ucfirst($type) . 'DefinitionRepositoryInterface');
        } catch (\ReflectionException $ex) {
            \App::abort(400, "The type " . $type . " is not supported.");
        }
    }

    /**
     * Return the properties (= column fields) for this model.
     */
    public function getCreateParameters()
    {
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
            'map_property' => array(
                'required' => false,
                'name' => 'Map property',
                'type' => 'string',
                'description' => 'The property (e.g. column name) of the dataset that will be shown when a map visualization is applicable. Non geo-graphical datasets are not affected by this property.',
            ),
        );
    }
}
