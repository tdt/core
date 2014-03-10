<?php

namespace repositories\interfaces;

interface XmlDefinitionRepositoryInterface
{

    /**
     * Return all XmlDefinition objects
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
     * Store a XmlDefinition
     *
     * @param array $input
     * @return array XmlDefinition
     */
    public function store(array $input);


    /**
     * Update a XmlDefinition
     *
     * @param integer $xml_id
     * @param array $input
     * @return array XmlDefinition
     */
    public function update($xml_id, array $input);

    /**
     * Delete a XmlDefinition
     *
     * @param integer $xml_id
     * @return boolean|null
     */
    public function delete($xml_id);

    /**
     * Fetch a XmlDefinition by id
     *
     * @param integer $xml_id
     * @return array XmlDefinition
     */
    public function getById($xml_id);
}
