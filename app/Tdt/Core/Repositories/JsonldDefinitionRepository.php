<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\JsonldDefinitionRepositoryInterface;
use Tdt\Core\DataControllers\JSONController;

class JsonldDefinitionRepository extends BaseDefinitionRepository implements JsonldDefinitionRepositoryInterface
{

    protected $rules = array(
        'uri' => 'json|uri|required',
        'description' => 'required',
    );

    public function __construct(\JsonldDefinition $model)
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
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            )
        );
    }
}
