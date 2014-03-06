<?php

namespace repositories\interfaces;

interface TabularColumnsRepositoryInterface{

    /**
     * Return all TabularColumns objects
     *
     * @return array
     */
    public function getAll();

    /**
     * Store a TabularColumn object
     *
     * @param array $input
     * @return array TabularColumn
     */
    public function store(array $input);

    /**
     * Delete a TabularColumn object
     *
     * @param integer $id
     * @return boolean|null
     */
    public function delete($id);

    /**
     * Validate the test_columns by matching correct_columns
     * often the test_columns are passed by the user, the correct columns
     * are the ones that are extracted from the datastructure (e.g. columns from a CSV, XLS, ...)
     *
     * @param array $correct_columns
     * @param array $test_columns
     * @return void | abort
     */
    public function validateBulk(array $correct_columns, array $test_columns);

    /**
     * Validate the input to a set of rules given an input
     *
     * @param array $input
     * @return void | abort
     */
    public function validate(array $input);

    /**
     * Retrieve all the column names mapped onto their aliases
     *
     * @param string $type
     * @param integer $id
     * @return array of TabularColumn's
     */
    public function getColumnAliases($id, $type);

    /**
     * Retrieve all the TabularColumn related to a type and id (polymorphic relationship)
     *
     * @param string $type
     * @param integer $id
     * @return array of TabularColumn's
     */
    public function getColumns($id, $type);

     /**
     * Store new TabularColumn relationships given by input
     *
     * @param integer $id
     * @param string $type
     * @param array $input
     * @return void
     */
    public function storeBulk($id, $type, $columns);

    /**
     * Delete all TabularColumn relationships for a given type and id
     *
     * @param integer $id
     * @param string $type
     * @return void
     */
    public function deleteBulk($id, $type);

    /**
     * Retrieve the set of create parameters that make up a TabularColumn
     * e.g. array( 'create_parameter' => array(
     *              'required' => true,
     *              'description' => '...',
     *              'type' => 'string',
     *              'name' => 'pretty name'
     *       ), ...)
     *
     * @return array TabularColumn
     */
    public function getCreateParameters();
}