<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface;
use Tdt\Core\DataControllers\JSONController;

class JsonDefinitionRepository extends BaseDefinitionRepository implements JsonDefinitionRepositoryInterface
{

    protected $rules = array(
        'uri' => 'json|required',
        'description' => 'required',
    );

    public function __construct(\JsonDefinition $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a JSON definition.
     */
    public function getCreateParameters()
    {
        return array(
            'uri' => array(
                'required' => true,
                'name' => 'URI',
                'description' => 'The location of the JSON file, this should either be a URL or a local file location.',
                'type' => 'string',
            ),
            'title' => array(
                'required' => true,
                'name' => 'Title',
                'description' => 'A name given to the resource.',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            ),
            'geo_formatted' => array(
                'required' => true,
                'name' => 'GeoJSON',
                'description' => 'Is the JSON document a GeoJSON document?',
                'type' => 'boolean',
                'default_value' => false,
            ),
        );
    }
}
