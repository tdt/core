<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\RemoteDefinitionRepositoryInterface;

class RemoteDefinitionRepository extends BaseDefinitionRepository implements RemoteDefinitionRepositoryInterface
{

    protected $rules = array(
        'dcat' => 'required',
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
            'dcat' => array(
                'required' => true,
                'name' => 'DCAT',
                'description' => 'The DCAT document for the dataset.',
                'type' => 'string',
            )
        );
    }
}
