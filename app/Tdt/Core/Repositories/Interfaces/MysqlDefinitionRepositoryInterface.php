<?php

namespace Tdt\Core\Repositories\Interfaces;

interface MysqlDefinitionRepositoryInterface
{

    /**
     * Return all CsvDefinition objects
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
     * Store a CsvDefinition object
     *
     * @param array $input
     * @return array CsvDefinition
     */
    public function store(array $input);

    /**
     * Update a CsvDefinition object
     *
     * @param integer $model_id
     * @param array $input
     * @return array CsvDefinition
     */
    public function update($model_id, array $input);

    /**
     * Delete a CsvDefinition
     *
     * @param integer $model_id
     * @return boolean|null
     */
    public function delete($model_id);

    /**
     * Fetch a CsvDefinition by id
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
     * (e.g. CSV needs columns, in an RDBMS as back-end this results in model relationships)
     * @return array
     */
    public function getAllParameters();
}
