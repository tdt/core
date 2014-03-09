<?php

namespace repositories\interfaces;

interface InstalledDefinitionRepositoryInterface
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
     * Store an InstalledDefinition
     *
     * @param array $input
     * @return array InstalledDefinition
     */
    public function store($input);


    /**
     * Update an InstalledDefinition
     *
     * @param integer $installed_id
     * @param array $input
     * @return array InstalledDefinition
     */
    public function update($installed_id, $input);

    /**
     * Delete an InstalledDefinition
     *
     * @param integer $installed_id
     * @return boolean|null
     */
    public function delete($installed_id);

    /**
     * Fetch an InstalledDefinition by id
     *
     * @param integer $installed_id
     * @return array InstalledDefinition
     */
    public function getById($installed_id);
}
