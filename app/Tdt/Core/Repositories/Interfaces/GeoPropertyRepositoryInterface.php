<?php

namespace Tdt\Core\Repositories\Interfaces;

interface GeoPropertyRepositoryInterface
{

    /**
     * Return all GeoProperty objects
     *
     * @return array
     */
    public function getAll();

    /**
     * Store a GeoProperty object
     *
     * @param array $input
     * @return array GeoProperty
     */
    public function store(array $input);

    /**
     * Delete a GeoProperty object
     *
     * @param integer $property_id
     * @return boolean|null
     */
    public function delete($property_id);

    /**
     * Retrieve all the GeoProperty related to a type and id (polymorphic relationship)
     *
     * @param string $property_id
     * @param integer $type
     * @return array of GeoProperty's
     */
    public function getGeoProperties($property_id, $type);

    /**
     * Validate the provided_geo by matching extracted_geo
     * often the provided_geo are passed by the user, the correct columns
     * are the ones that are extracted from the datastructure (e.g. columns from a CSV, XLS, ...)
     *
     * @param array $extracted_geo
     * @param array $provided_geo
     * @return void | abort
     */
    public function validateBulk(array $extracted_geo, array $provided_geo);

    /**
     * Validate the input to a set of rules given an input
     *
     * @param array $input
     * @return void | abort
     */
    public function validate(array $input);

    /**
     * Store new GeoProperty objects given by input to a certain source
     *
     * @param integer $property_id
     * @param string $type
     * @param array $input
     * @return void
     */
    public function storeBulk($property_id, $type, array $input);

    /**
     * Delete all GeoProperty relationships for a given type and id
     *
     * @param integer $property_id
     * @param string $type
     * @return void
     */
    public function deleteBulk($property_id, $type);

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
