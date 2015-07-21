<?php

namespace Tdt\Core\Repositories\Interfaces;

interface MongoDefinitionRepositoryInterface
{

    /**
     * Return all MongoDefinition objects
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
     * Store a MongoDefinition object
     *
     * @param array $input
     * @return array MongoDefinition
     */
    public function store(array $input);

    /**
     * Update a MongoDefinition object
     *
     * @param integer $model_id
     * @param array $input
     * @return array MongoDefinition
     */
    public function update($model_id, array $input);

    /**
     * Delete a MongoDefinition
     *
     * @param integer $model_id
     * @return boolean|null
     */
    public function delete($model_id);

    /**
     * Fetch a MongoDefinition by id
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
     * (e.g. Mysql needs columns, in an RDBMS as back-end this results in model relationships)
     * @return array
     */
    public function getAllParameters();
}
