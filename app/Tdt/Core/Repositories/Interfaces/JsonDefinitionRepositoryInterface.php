<?php

namespace Tdt\Core\Repositories\Interfaces;

interface JsonDefinitionRepositoryInterface
{

    /**
     * Return all JsonDefinition objects
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
     * @return array
     */
    public function getAllParameters();

    /**
     * Store a JsonDefinition
     *
     * @param array $input
     * @return array JsonDefinition
     */
    public function store(array $input);


    /**
     * Update a JsonDefinition
     *
     * @param integer $id
     * @param array $input
     * @return array JsonDefinition
     */
    public function update($id, array $input);

    /**
     * Delete a JsonDefinition
     *
     * @param integer $id
     * @return boolean|null
     */
    public function delete($id);

    /**
     * Fetch a JsonDefinition by id
     *
     * @param integer $id
     * @return array JsonDefinition
     */
    public function getById($id);
}
