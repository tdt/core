<?php

namespace repositories\interfaces;

interface XmlDefinitionRepositoryInterface
{

    /**
     * Return a validator based on an hash array
     *
     * @param array $input
     * return mixed
     */
    public function getValidator($input);

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
     * Store a XmlDefinition
     *
     * @param array $input
     * @return array XmlDefinition
     */
    public function store($input);


    /**
     * Update a XmlDefinition
     *
     * @param integer $id
     * @param array $input
     * @return array XmlDefinition
     */
    public function update($id, $input);

    /**
     * Delete a XmlDefinition
     *
     * @param integer $id
     * @return boolean|null
     */
    public function delete($id);

    /**
     * Fetch a XmlDefinition by id
     *
     * @param integer $id
     * @return array XmlDefinition
     */
    public function getById($id);
}
