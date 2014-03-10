<?php

namespace repositories\interfaces;

interface XlsDefinitionRepositoryInterface
{

    /**
     * Return all XlsDefinition objects
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
     * Store a XlsDefinition object
     *
     * @param array $input
     * @return array XlsDefinition
     */
    public function store(array $input);

    /**
     * Update a XlsDefinition object
     *
     * @param integer $xls_id
     * @param array $input
     * @return array XlsDefinition
     */
    public function update($xls_id, array $input);

    /**
     * Delete a XlsDefinition
     *
     * @param integer $xls_id
     * @return boolean|null
     */
    public function delete($xls_id);

    /**
     * Fetch a XlsDefinition by id
     *
     * @param integer $xls_id
     * @return array object
     */
    public function getById($xls_id);

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
