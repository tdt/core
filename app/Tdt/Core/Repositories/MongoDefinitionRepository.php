<?php namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\MongoDefinitionRepositoryInterface;

class MongoDefinitionRepository extends BaseDefinitionRepository implements MongoDefinitionRepositoryInterface
{
    protected $rules = array(
        'host' => 'required',
        'port' => 'integer',
        'database' => 'required',
        'mongo_collection' => 'required',
        'description' => 'required',
    );

    public function __construct(\MongoDefinition $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a Mongo definition.
     * Include the parameters that make up relationships with this model.
     */
    public function getAllParameters()
    {
        return array_merge($this->getCreateParameters());
    }

    /**
     * Return the properties (= column fields) for this model.
     */
    public function getCreateParameters()
    {
        return array(
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'text',
            ),
            'host' => array(
                'required' => true,
                'name' => 'Host',
                'description' => 'The host of the MongoDB database.',
                'type' => 'string',
            ),
            'database' => array(
                'required' => true,
                'name' => 'Database',
                'description' => 'The name of the database where the collection, that needs to be published, resides.',
                'type' => 'string',
            ),
            'mongo_collection' => array(
                'required' => true,
                'name' => 'Collection',
                'description' => 'The collection of the database.',
                'type' => 'string',
            ),
            'port' => array(
                'required' => false,
                'name' => 'Port',
                'description' => 'The port of the Mongo database where a connection can be set up.',
                'type' => 'string',
                'default_value' => 27017
            ),
            'username' => array(
                'required' => false,
                'name' => 'Username',
                'description' => 'A username that has read permissions on the provided collection. Safety first, make sure the user only has read permissions.',
                'type' => 'string',
            ),
            'password' => array(
                'required' => false,
                'name' => 'Password',
                'description' => 'The password for the user that has read permissions.',
                'type' => 'string',
            ),
        );
    }
}
