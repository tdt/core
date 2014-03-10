<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\InstalledDefinitionRepositoryInterface;

class InstalledDefinitionRepository extends BaseDefinitionRepository implements InstalledDefinitionRepositoryInterface
{

    protected $rules = array(
        'class' => 'required',
        'path' => 'installed|required',
        'description' => 'required',
    );

    function __construct(\InstalledDefinition $model) {
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a installed definition.
     */
    public function getCreateParameters()
    {
        return array(
            'class' => array(
                'required' => true,
                'name' => 'Class name',
                'description' => 'The name of the class',
                'type' => 'string',
            ),
            'path' => array(
                'required' => true,
                'name' => 'Class file path',
                'description' => 'The location of the class file, relative from the "/installed" folder.',
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
