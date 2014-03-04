<?php

namespace repositories\interfaces;

interface BaseRepositoryInterface{

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
     * (e.g. CSV needs columns, in an RDBMS as back-end this results in model relationships)
     * @return array
     */
    public function getAllParameters();

    /**
     * Store an object
     *
     * @param array $input
     * @return array object
     */
    public function store($input);


    /**
     * Update an object
     *
     * @param integer $id
     * @param array $input
     * @return array object
     */
    public function update($id, $input);

    /**
     * Delete an object
     *
     * @param integer $id
     * @return boolean|null
     */
    public function delete($id);

    /**
     * Fetch an object by id
     *
     * @param integer $id
     * @return array object
     */
    public function getById($id);

}