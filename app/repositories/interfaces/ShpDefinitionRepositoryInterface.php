<?php

namespace repositories\interfaces;

interface ShpDefinitionRepositoryInterface
{

    /**
     * Return all ShpDefinition objects
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
     * Store a ShpDefinition
     *
     * @param array $input
     * @return array ShpDefinition
     */
    public function store(array $input);


    /**
     * Update a ShpDefinition
     *
     * @param integer $shp_id
     * @param array $input
     * @return array ShpDefinition
     */
    public function update($shp_id, array $input);

    /**
     * Delete a ShpDefinition
     *
     * @param integer $shp_id
     * @return boolean|null
     */
    public function delete($shp_id);

    /**
     * Fetch a ShpDefinition by id
     *
     * @param integer $shp_id
     * @return array ShpDefinition
     */
    public function getById($shp_id);
}
