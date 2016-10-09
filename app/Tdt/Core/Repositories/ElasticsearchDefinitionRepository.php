<?php namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\ElasticsearchDefinitionRepositoryInterface;
use Load\Elasticsearch;

class ElasticsearchDefinitionRepository extends BaseDefinitionRepository implements ElasticsearchDefinitionRepositoryInterface
{
    protected $rules = array(
        'host' => 'required',
        'port' => 'integer',
        'es_index' => 'required',
        'es_type' => 'required',
        'description' => 'required',
    );

    public function __construct(\ElasticsearchDefinition $model)
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

    public function update($model_id, array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $model_object = $this->model->find($model_id);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));

        $input = $this->addOriginalFile($input);

        $model_object->update($input);

        return $model_object->toArray();
    }

    /**
     * Check if we can get the original file by looking for the passed index and document type
     * in the jobs (CSV is only supported for now to be a computed original file field)
     *
     * @param  array $input
     * @return array
     */
    private function addOriginalFile($input)
    {
        $es_job = Elasticsearch::where('es_index', $input['es_index'])
                    ->where('es_type', $input['es_type'])
                    ->where('host', $input['host'])
                    ->first();

        if (! empty($es_job)) {
            $extractor = $es_job->job->extractor()->first();

            if (strtolower($extractor->type) == 'csv') {
                $input['original_file'] = $extractor->uri;
            }
        }

        return $input;
    }

    public function store(array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $input = $this->addOriginalFile($input);

        return $this->model->create(array_only($input, array_keys($this->getCreateParameters())));
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
            'es_index' => array(
                'required' => true,
                'name' => 'Index',
                'description' => 'The name of the index where data of a certain type resides.',
                'type' => 'string',
            ),
            'es_type' => array(
                'required' => true,
                'name' => 'Type',
                'description' => 'The type of data that needs to be published.',
                'type' => 'string',
            ),
            'port' => array(
                'required' => false,
                'name' => 'Port',
                'description' => 'The port of the Elasticsearch database where a connection can be set up.',
                'type' => 'string',
                'default_value' => 9200
            ),
            'username' => array(
                'required' => false,
                'name' => 'Username',
                'description' => 'A username that has read permissions on the provided index. Safety first, make sure the user only has read permissions.',
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
