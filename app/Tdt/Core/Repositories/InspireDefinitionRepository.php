<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\InspireDefinitionRepositoryInterface;

class InspireDefinitionRepository extends BaseDefinitionRepository implements InspireDefinitionRepositoryInterface
{

    protected $rules = array(
        'original_document' => 'required',
    );

    public function __construct(\InspireDefinition $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a XML definition.
     */
    public function getCreateParameters()
    {
        return array(
            'original_document' => array(
                'required' => true,
                'name' => 'Original document',
                'description' => 'Het origineel inspire XML document.',
                'type' => 'string',
            )
        );
    }
}
