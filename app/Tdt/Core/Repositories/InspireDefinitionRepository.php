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
                'description' => 'The original INSPIRE XML document.',
                'type' => 'string',
            ),
            'dcat' => array(
                'required' => false,
                'name' => 'GeoDCAT',
                'description' => 'The GeoDCAT document representation of the original INSPIRE document. (translation will be automatic from the INSPIRE document to GeoDCAT)',
                'type' => 'text',
            ),
        );
    }
}
