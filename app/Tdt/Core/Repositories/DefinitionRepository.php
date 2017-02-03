<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Tdt\Core\Repositories\LocationRepository;
use Illuminate\Support\Facades\Validator;
use Tdt\Core\Formatters\FormatHelper;

class DefinitionRepository extends BaseDefinitionRepository implements DefinitionRepositoryInterface
{
    protected $dcat_rules = [
        'resource_name' => 'required',
        'collection_uri' => 'required|collectionuri',
        'contact_point' => 'uri',
    ];

    protected $geodcat_rules = [
        'resource_name' => 'required',
        'collection_uri' => 'required|collectionuri',
        'contact_point' => 'uri',
        'geometry' => 'required',
        'label' => 'required',
        'contact_point' => 'required',
        'language' => 'required',
        'rights' => 'required',
        'date' => 'required'
    ];

    public function __construct(\Definition $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new definition with corresponding source type
     */
    public function store(array $input)
    {
        $attributions = @$input['attribution'];

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
        $create_parameters = $this->getCreateParameters();
        unset($create_parameters['geometry']);
        unset($create_parameters['label']);

        foreach (array_only($input, array_keys($create_parameters)) as $property => $value) {
            $definition->$property = $value;
        }

        if (!empty($source['title'])) {
            $definition->title = $source['title'];
        }

        if (!empty($source['description'])) {
            $definition->description = $source['description'];
        }

        $location = $this->createLocation($input);

        // Check for location meta-data
        if (!empty($location)) {
            $definition->location()->save($location);
        }

        // Add attributions if there are any
        if (!empty($attributions)) {
            $attribution_models = [];

            foreach ($attributions as $attribution) {
                $attribution_models[] = $this->createAttribution($attribution);
            }

            $definition->attributions()->saveMany($attribution_models);
        }

        $definition->save();

        // Update the facets for the definition
        $this->updateFacets($definition);

        return $definition->toArray();
    }

    public function update($identifier, array $input)
    {
        $attributions = @$input['attribution'];

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

        $definition_model = \Definition::find($definition['id']);

        $definition_model->update(array_only($input, array_keys($this->getCreateParameters())));

        if (!empty($input['title'])) {
            $definition_model->title = $input['title'];
        }

        if (!empty($input['description'])) {
            $definition_model->description = $input['description'];
        }

        // Update the facets for the definition
        $this->updateFacets($definition_model);

        //$definition_object = $this->getEloquentDefinition($identifier);

        // Add the rest of the properties
        $create_parameters = $this->getCreateParameters();
        unset($create_parameters['geometry']);
        unset($create_parameters['label']);

        $definition_model->update(array_only($input, array_keys($create_parameters)));

        // Delete the locations and create again if geo meta-data is provided (= update)
        if (!empty($definition_model->location->id)) {
            $location = $definition_model->location;
            $location->geometry->delete();
            $location->label->delete();
            $location->delete();

            $definition_model->save();
        }

        $location = $this->createLocation($input);

        // Check for location meta-data
        if (!empty($location)) {
            $definition_model->location()->save($location);
        }

        if (!empty($definition_model->attributions)) {
            foreach ($definition_model->attributions as $attribution) {
                $attribution->delete();
            }
        }

        // Add attributions if there are any
        if (!empty($attributions)) {
            $attribution_models = [];

            foreach ($attributions as $attribution) {
                $attribution_models[] = $this->createAttribution($attribution);
            }

            $definition_model->attributions()->saveMany($attribution_models);
        }

        $definition_model->save();

        return $definition_model->toArray();
    }

    /**
     * Delete a definition
     */
    public function delete($identifier)
    {
        $definition = $this->getEloquentDefinition($identifier);

        if (!empty($definition)) {
            if (!empty($definition->location->id)) {
                $location = $definition->location;

                $location->delete();
            }

            foreach ($definition->attributions as $attribution) {
                $attribution->delete();
            }

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
        return \Definition::take($limit)->skip($offset)->with('location', 'attributions')->get()->toArray();
    }

    public function getAllPublished($limit = PHP_INT_MAX, $offset = 0)
    {
        return \Definition::take($limit)->skip($offset)->get()->toArray();
    }

    public function getFiltered($filters, $limit, $offset)
    {
        $query = $this->model->query();

        if (!empty($filters['query'])) {
            $search_value = array_shift($filters['query']);

            unset($filters['query']);

            $query->where(function ($subquery) use ($search_value) {
                return $subquery->where('title', 'LIKE', '%' . $search_value . '%')
                        ->orWhere('description', 'LIKE', '%' . $search_value . '%')
                        ->orWhere('resource_name', 'LIKE', '%' . $search_value . '%')
                        ->orWhere('collection_uri', 'LIKE', '%' . $search_value . '%');
            });
        }

        foreach ($filters as $filter => $values) {
            $query->where(function ($subquery) use ($filter, $values) {
                $val = array_shift($values);

                $subquery->where($filter, 'LIKE', '%' . $val . '%');

                foreach ($values as $val) {
                    $subquery->orWhere($filter, 'LIKE', '%' . $val . '%');
                }

                return $subquery;
            });
        }

        $results = $query->take($limit)->skip($offset)->get();

        if (!empty($results)) {
            $definitions_info = [];

            foreach ($results as $result) {
                $info = array_only($result->toArray(), $this->model->getFillable());
                $info['identifier'] = $result->collection_uri . '/' . $result->resource_name;

                $definitions_info[] = $info;
            }
            return $definitions_info;
        }

        return [];

    }

    public function countFiltered($filters, $limit, $offset)
    {
        $query = $this->model->query();

        if (!empty($filters['query'])) {
            $search_value = array_shift($filters['query']);

            unset($filters['query']);

            $query->where(function ($subquery) use ($search_value) {
                return $subquery->where('title', 'LIKE', '%' . $search_value . '%')
                        ->orWhere('description', 'LIKE', '%' . $search_value . '%')
                        ->orWhere('resource_name', 'LIKE', '%' . $search_value . '%')
                        ->orWhere('collection_uri', 'LIKE', '%' . $search_value . '%');
            });
        }

        foreach ($filters as $filter => $values) {
            $query->where(function ($subquery) use ($filter, $values) {
                $val = array_shift($values);

                $subquery->where($filter, 'LIKE', '%' . $val . '%');

                foreach ($values as $val) {
                    $subquery->orWhere($filter, 'LIKE', '%' . $val . '%');
                }

                return $subquery;
            });
        }


        return $query->count();
    }

    public function countFacets($filters)
    {
        $query = \DB::table('definition_facets')
                    ->select(\DB::raw('count(*) as count, facet_name, value'));


        $facet_subquery = \DB::table('definitions')->select('id');

        if (!empty($filters['query'])) {
            $search_value = array_shift($filters['query']);

            unset($filters['query']);

            $facet_subquery->where(function ($facet_subquery) use ($search_value) {
                return $facet_subquery->where('title', 'LIKE', '%' . $search_value . '%')
                ->orWhere('description', 'LIKE', '%' . $search_value . '%')
                ->orWhere('resource_name', 'LIKE', '%' . $search_value . '%')
                ->orWhere('collection_uri', 'LIKE', '%' . $search_value . '%');
            });
        }

        foreach ($filters as $filter => $values) {
            $facet_subquery->where(function ($facet_subquery) use ($filter, $values) {
                $val = array_shift($values);

                $facet_subquery->where($filter, 'LIKE', '%' . $val . '%');

                foreach ($values as $val) {
                    $facet_subquery->orWhere($filter, 'LIKE', '%' . $val . '%');
                }

                return $facet_subquery;
            });
        }

        $results = $query->whereRaw('definition_id IN (' . $facet_subquery->toSql() . ')')
                        ->mergeBindings($facet_subquery)
                        ->groupBy('value', 'facet_name')
                        ->get();

        return $results;
    }

    public function getByIdentifier($identifier)
    {
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($identifier . '/'))->with('location', 'attributions')->first();

        if (empty($definition)) {
            return [];
        }

        return $definition->toArray();
    }


    public function getByCollection($collection)
    {
        $collection = \Definition::whereRaw("CONCAT(collection_uri, '/') like CONCAT(?, '%')", array($collection . '/'))->get();

        if (!empty($collection)) {
            return $collection->toArray();
        }

        return $collection;
    }

    public function getOldest()
    {
        $definition = \Definition::where('updated_at', '=', \DB::table('definitions')->max('updated_at'))->first();

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
        return \Definition::count();
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
            \App::abort(400, $message);
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

    public function getAllDefinitionInfo($limit, $offset, $keywords = [])
    {
        if (!empty($keywords)) {
            $filtered_definitions = [];

            $count = 0;
            $skipped = 0;

            foreach ($keywords as $keyword) {
                $definitions = \Definition::where('keywords', 'LIKE', '%' . $keyword . '%')->get();

                $definition_count = $definitions->count();

                if ($definition_count >= $offset || $count <= $limit + $offset) {
                    // Use the slice(offset, amount) function on the Collection object
                    if ($count < $offset) {
                        $eligable_definitions = $definitions->slice($offset - $count, $limit - $count);

                        foreach ($eligable_definitions as $eligable_definition) {
                            $filtered_definitions[] = $eligable_definition->toArray();
                        }
                    } elseif ($count < $limit + $offset) {
                        $eligable_definitions = $definitions->slice(0, $limit - $count + 1);

                        foreach ($eligable_definitions as $eligable_definition) {
                            $filtered_definitions[] = $eligable_definition->toArray();
                        }
                    }
                }

                $count += $definition_count;
            }

            $filtered_info = [];

            foreach ($filtered_definitions as $filtered_definition) {
                $identifier = $filtered_definition['collection_uri'] . '/' . $filtered_definition['resource_name'];
                $filtered_info[$identifier] = $this->getDescriptionInfo($identifier);
            }

            return $filtered_info;

        } else {
            $definitions = array();

            foreach ($this->getAll($limit, $offset) as $definition) {
                $identifier = $definition['collection_uri'] . '/' . $definition['resource_name'];
                $definitions[$identifier] = $this->getDescriptionInfo($identifier);
            }
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
        $properties['description'] = @$source_definition->description;

        // Get the formats based on the source definition meta-data
        $format_helper = new FormatHelper();
        $formats = $format_helper->getFormatsForType($source_definition->toArray());

        $properties['formats'] = $formats;
        $properties['identifier'] = $identifier;

        unset($properties['map_property']);

        return $properties;
    }

    /**
     * Create and return a location
     *
     * @param array $input
     *
     * @return \Location
     */
    private function createLocation($input)
    {
        if (!empty($input['geometry'])) {
            $location = new \Location();
            $location->save();

            if (empty($input['label'])) {
                \App::abort(400, 'Geographical label cannot be empty.');
            }

            $label = \Label::create(['label' => $input['label']]);

            $geometry = \Geometry::create(['type' => 'geojson', 'geometry' => $input['geometry']]);

            $location->geometry()->save($geometry);
            $location->label()->save($label);

            return $location;
        } else {
            return null;
        }
    }

    /**
     * Create and return an attribution
     *
     * @param array $input
     *
     * @return \Attribution
     */
    private function createAttribution($attribution)
    {
        $attribution = \Attribution::create($attribution);

        $attribution->save();

        return $attribution;
    }

    /**
     * Get the eloquent object of a definition
     *
     * @param string $identifier The identifier of the dataset definition
     *
     * @return Eloquent
     */
    private function getEloquentDefinition($identifier)
    {
        return \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($identifier . '/'))->with(['location', 'attributions'])->first();
    }

    /**
     * Update the facets for a definition
     *
     * @param Eloquent $definition
     *
     * @return void
     */
    private function updateFacets($definition)
    {
        $facet_types = \FacetType::all()->toArray();

        foreach ($definition->facets() as $facet) {
            \Facet::delete($facet['id']);
        }

        foreach ($facet_types as $facet_type) {
            $facet_name = $facet_type['facet_name'];

            if (!empty($definition->$facet_name)) {
                $facet = \Facet::create([
                    'definition_id' => $definition->id,
                    'facet_id' => $facet_type['id'],
                    'facet_name' => $facet_type['facet_name'],
                    'value' => $definition->$facet_name
                    ]);

                $facet->save();
            }
        }

        // Process keywords if present
        if (!empty($definition->keywords)) {
            // Split the keywords
            $keywords = explode(',', $definition->keywords);

            foreach ($keywords as $keyword) {
                $facet = \Facet::create([
                    'definition_id' => $definition->id,
                    'facet_id' => $facet_type['id'],
                    'facet_name' => 'keyword',
                    'value' => $keyword
                    ]);

                $facet->save();
            }
        }
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

        if (!empty($definition->location->id)) {
            $location = \Location::find($definition->location->id)->with('label', 'geometry')->first();

            if (!empty($location)) {
                $properties['spatial'] = $location->toArray();
            }
        }

        if ($definition->attributions->count() > 0) {
            $properties['attributions'] = [];

            foreach ($definition->attributions as $attribution) {
                $properties['attributions'][] = array_only($attribution->toArray(), $attribution->getFillable());
            }
        }

        return $properties;
    }

    public function getValidator(array $input)
    {
        if (!empty($input['profile']) && $input['profile'] == 'geodcat') {
            return Validator::make($input, $this->geodcat_rules, $this->error_messages);
        } else {
            return Validator::make($input, $this->dcat_rules, $this->error_messages);
        }
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
            'date' => array(
                'required' => false,
                'requiredgeodcat' => 'required',
                'name' => 'Date',
                'description' => 'A date associated with the dataset.',
                'type' => 'date',
                'group' => 'dc',
            ),
            'language' => array(
                'required' => false,
                'requiredgeodcat' => 'required',
                'name' => 'Language',
                'description' => 'A language of the resource.',
                'type' => 'list',
                'list' => 'api/languages',
                'list_option' => 'name',
                'group' => 'dc',
            ),
            'rights' => array(
                'required' => false,
                'requiredgeodcat' => 'required',
                'name' => 'License',
                'type' => 'list',
                'list' => 'api/licenses',
                'list_option' => 'title',
                'description' => 'Information about rights held in and over the resource.',
                'group' => 'dc',
                'default_value' => 'License Not Specified'
            ),
            'theme' => array(
                'required' => false,
                'requiredgeodcat' => false,
                'name' => 'Theme',
                'type' => 'list',
                'list' => 'api/themes',
                'list_option' => 'label',
                'description' => 'The theme or category that the dataset belongs to.',
                'group' => 'dc',
            ),
            'cache_minutes' => array(
                'required' => false,
                'name' => 'Cache',
                'type' => 'integer',
                'description' => 'How long this resource should be cached (in minutes).',
            ),
            'publisher_uri' => array(
                'required' => false,
                'requiredgeodcat' => 'recommended',
                'name' => 'Publisher URI',
                'type' => 'string',
                'description' => 'The URI of the entity responsible for publishing the dataset (e.g. http://gov.be). ',
                'group' => 'dc',
            ),
            'publisher_name' => array(
                'required' => false,
                'requiredgeodcat' => false,
                'name' => 'Publisher name',
                'type' => 'string',
                'description' => 'The name of the entity responsible for publishing the dataset.',
                'group' => 'dc',
            ),
            'job_id' => array(
                'required' => false,
                'name' => 'Related job',
                'type' => 'integer',
                'description' => 'Job linked to this dataset.',
            ),
            'original_file' => array(
                'required' => false,
                'name' => 'Original file',
                'type' => 'string',
                'description' => 'Original dataset file.',
            ),
            'draft_flag' => array(
                'required' => false,
                'name' => 'draft flag',
                'type' => 'integer',
                'description' => 'draft flag',
            ),
            'job_id' => array(
                'required' => false,
                'name' => 'Related job',
                'type' => 'integer',
                'description' => 'Job linked to this dataset.',
            ),
            'original_file' => array(
                'required' => false,
                'name' => 'Original file',
                'type' => 'string',
                'description' => 'Original dataset file.',
            ),
            'draft_flag' => array(
                'required' => false,
                'name' => 'draft flag',
                'type' => 'integer',
                'description' => 'draft flag',
            ),
            'user_id' => array(
                'required' => true,
                'name' => 'User id',
                'type' => 'integer',
                'description' => 'User (id) who created this dataset.',
            ),
            'username' => array(
                'required' => true,
                'name' => 'Username',
                'type' => 'string',
                'description' => 'User (username) who created this dataset.',
            ),
            'keywords' => array(
                'required' => false,
                'requiredgeodcat' => 'required',
                'name' => 'Keywords',
                'type' => 'string',
                'description' => 'A comma separated list of keywords regarding the dataset.',
                'group' => 'dc',
            ),
            'contact_point' => array(
                'required' => false,
                'requiredgeodcat' => 'required',
                'name' => 'Contact point',
                'type' => 'string',
                'description' => 'A URI on which people can provide feedback or flag errors.',
                'group' => 'dc',
            ),
            'geometry' => array(
                'required' => true,
                'requiredgeodcat' => 'required',
                'name' => 'Geometry',
                'type' => 'geojson',
                'description' => 'A GeoJSON document that represents the geographical relevancy of the dataset.',
                'group' => 'geodcat',
                'array' => true
            ),
            'label' => array(
                'required' => false,
                'requiredgeodcat' => 'required',
                'name' => 'Label',
                'type' => 'string',
                'description' => 'A description of the selected geographical area.',
                'group' => 'geodcat',
                'array' => true
            )
        );
    }
}
