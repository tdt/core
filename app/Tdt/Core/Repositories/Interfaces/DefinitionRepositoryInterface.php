<?php

namespace Tdt\Core\Repositories\Interfaces;

interface DefinitionRepositoryInterface
{

    /**
     * Store a Definition object
     *
     * @param array $input
     * @return array Definition
     */
    public function store(array $input);

    /**
     * Update a Definition object
     *
     * @param string $identifier
     * @param array $input
     * @return array Definition
     */
    public function update($identifier, array $input);

    /**
     * Delete a Definition object
     *
     * @param string $identifier
     * @return boolean|null
     */
    public function delete($identifier);

    /**
     * Check if a definition exists with a given identifier
     *
     * @param string $identifier
     * @return boolean
     */
    public function exists($identifier);

    /**
     * Retrieve all definitions
     *
     * @param integer $limit
     * @param integer $offset
     * @return array of Definition's
     */
    public function getAll($limit, $offset);

    /**
     * Retrieve all published definitions
     *
     * @param integer $limit
     * @param integer $offset
     * @return array of Definition's
     */
    public function getAllPublished($limit, $offset);

    /**
     * Retrieve a definition by its identifier
     *
     * @param string $identifier
     * @return array Definition
     */
    public function getByIdentifier($identifier);

    /**
     * Retrieve a collection of definitions
     * based on the collection prefix of the identifier
     *
     * @param string $identifier
     * @return array of Definition's
     */
    public function getByCollection($collection);

    /**
     * Retrieve the oldest definition
     *
     * @return array Definition
     */
    public function getOldest();

    /**
     * Retrieve the amount of definitions
     *
     * @return integer
     */
    public function count();

    /**
     * Retrieve the amount of published definitions
     *
     * @return integer
     */
    public function countPublished();

    /**
     * Get the source of the definition (e.g. CsvDefinition, ShpDefinition,...)
     *
     * @param string $name (e.g. CsvDefinition, ShpDefinition,...)
     * @param integer $definition_id (id of the definition)
     * @return array Source
     */
    public function getDefinitionSource($definition_id, $name);

    /**
     * Retrieve all the information of all Defintion's
     *
     * @param string $identifier (optional)
     * @param integer $limit
     * @param integer $offset
     * @return array of Definition's
     */
    public function getAllFullDescriptions($identifier, $limit, $offset);

    /**
     * Retrieve all the public information of all published Defintion's
     *
     * @param string $identifier (optional)
     * @param integer $limit
     * @param integer $offset
     * @return array of Definition's
     */
    public function getAllDefinitionInfo($identifier, $limit, $offset);

    /**
     * Retrieve the full description of a definition
     *
     * @param string $identifier
     * @return array Definition
     */
    public function getFullDescription($identifier);

    /**
     * Retrieve the create parameters for a Definition
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
}
