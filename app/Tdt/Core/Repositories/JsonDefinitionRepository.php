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
                'type' => 'text',
            ),
            'jsontype' => array(
                'required' => true,
                'name' => 'JSON type',
                'description' => 'What kind of JSON is it?',
                'type' => 'list',
                'list' => 'Plain|GeoJSON|JSON-LD',
                'default_value' => false,
            ),
        );
    }
}
