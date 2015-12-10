<?php

namespace Tdt\Core\Repositories\Interfaces;

interface ElasticsearchDefinitionRepositoryInterface
{

    /**
     * Return all ElasticsearchDefinition objects
     *
     * @return array
     */
    public function getAll();

    /**
     * Return a validator based on an hash array
     *
     * @param array $input
     * return Illuminate\Validation\Validator
     */
    public function getValidator(array $input);

    /**
     * Store a ElasticsearchDefinition object
     *
     * @param array $input
     * @return array ElasticsearchDefinition
     */
    public function store(array $input);

    /**
     * Update a ElasticsearchDefinition object
     *
     * @param integer $model_id
     * @param array $input
     * @return array ElasticsearchDefinition
     */
    public function update($model_id, array $input);

    /**
     * Delete a ElasticsearchDefinition
     *
     * @param integer $model_id
     * @return boolean|null
     */
    public function delete($model_id);

    /**
     * Fetch a ElasticsearchDefinition by id
     *
     * @param integer $model_id
     * @return array object
     */
    public function getById($model_id);

    /**
     * Return an array of create parameters with info attached
     * e.g. array( 'create_parameter' => array(
     *              'required' => true,
     *              'description' => '...',
     *              'type' => 'string',
     *              'name' => 'pretty name'
     *       ), ...)
     *
     * @return array
     */
    public function getCreateParameters();

    /**
     * Return an array of all the create parameters, also the parameters
     * that are necessary for further internal relationships
     *
     *
     * @return array
     */
    public function getAllParameters();
}
