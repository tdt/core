<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\RemoteDefinitionRepositoryInterface;

class RemoteDefinitionRepository extends BaseDefinitionRepository implements RemoteDefinitionRepositoryInterface
{

    protected $rules = array(
        'dataset_uri' => 'uri|required',
    );

    public function __construct(\RemoteDefinition $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a XML definition.
     */
    public function getCreateParameters()
    {
        return array(
            'dataset_uri' => array(
                'required' => true,
                'name' => 'URI',
                'description' => 'The URI of the remote dataset.',
                'type' => 'string',
            )
        );
    }
}
