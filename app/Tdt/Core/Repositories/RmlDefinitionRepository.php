<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\RmlDefinitionRepositoryInterface;

class RmlDefinitionRepository extends BaseDefinitionRepository implements RmlDefinitionRepositoryInterface
{

    protected $rules = array(
        'mapping_document' => 'json|uri|required',
        'output_file' => '',
        'description' => 'required',
    );

    public function __construct(\RmlDefinition $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a JSON definition.
     */
    public function getCreateParameters()
    {
        return array(
            'mapping_document' => array(
                'required' => true,
                'name' => 'Mapping document',
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
            )
        );
    }
}
