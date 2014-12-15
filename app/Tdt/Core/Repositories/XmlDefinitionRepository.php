<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\XmlDefinitionRepositoryInterface;

class XmlDefinitionRepository extends BaseDefinitionRepository implements XmlDefinitionRepositoryInterface
{

    protected $rules = array(
        'uri' => 'uri|required',
        'description' => 'required',
    );

    public function __construct(\XmlDefinition $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a XML definition.
     */
    public function getCreateParameters()
    {
        return array(
            'uri' => array(
                'required' => true,
                'name' => 'URI',
                'description' => 'The location of the XML file, this should either be a URL or a local file location.',
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
