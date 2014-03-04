<?php

namespace repositories\interfaces;

interface GeoPropertyRepositoryInterface{

    /**
     * Store a GeoProperty object
     *
     * @param array $input
     * @return array GeoProperty
     */
    public function store($input);

    /**
     * Delete a GeoProperty object
     *
     * @param integer $id
     * @return boolean|null
     */
    public function delete($id);

    /**
     * Retrieve all the GeoProperty related to a type and id (polymorphic relationship)
     *
     * @param string $id
     * @param integer $type
     * @return array of GeoProperty's
     */
    public function getGeoProperties($id, $type);

    /**
     * Validate the input to a set of rules given an input
     *
     * @param array $input
     * @return void | abort
     */
    public function validate($input);

    /**
     * Store new GeoProperty objects given by input to a certain source
     *
     * @param integer $id
     * @param string $type
     * @param array $input
     * @return void
     */
    public function storeBulk($id, $type, $input);

    /**
     * Delete all GeoProperty relationships for a given type and id
     *
     * @param integer $id
     * @param string $type
     * @return void
     */
    public function deleteBulk($id, $type);

    /**
     * Retrieve the set of create parameters that make up a GeoProperty
     * e.g. array( 'create_parameter' => array(
     *              'required' => true,
     *              'description' => '...',
     *              'type' => 'string',
     *              'name' => 'pretty name'
     *       ), ...)
     *
     * @return array GeoProperty
     */
    public function getCreateParameters();
}