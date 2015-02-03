<?php

namespace Tdt\Core\Repositories\Interfaces;

interface RmlDefinitionRepositoryInterface
{

    /**
     * Return all RmlDefinition objects
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
     * Store a RmlDefinition
     *
     * @param array $input
     * @return array RmlDefinition
     */
    public function store(array $input);


    /**
     * Update a RmlDefinition
     *
     * @param integer $id
     * @param array $input
     * @return array RmlDefinition
     */
    public function update($id, array $input);

    /**
     * Delete a RmlDefinition
     *
     * @param integer $id
     * @return boolean|null
     */
    public function delete($id);

    /**
     * Fetch a RmlDefinition by id
     *
     * @param integer $id
     * @return array RmlDefinition
     */
    public function getById($id);
}
