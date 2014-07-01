<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\RdfDefinitionRepositoryInterface;

/**
 * Repository for the rdf definitions
 *
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class RdfDefinitionRepository extends BaseDefinitionRepository implements RdfDefinitionRepositoryInterface
{
    protected $rules = array(
        'uri' => 'required|uri',
        'description' => 'required'
    );

    public function __construct(\RdfDefinition $model)
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
                'description' => 'The URI of the turtle file.',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            ),
            'format' => array(
                'required' => true,
                'name' => 'Format',
                'description' => 'The format of your RDF content, Turtle, XML, ...',
                'type' => 'string',
            ),
        );
    }
}
