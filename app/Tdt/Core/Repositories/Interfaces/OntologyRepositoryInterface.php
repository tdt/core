<?php

namespace Tdt\Core\Repositories\Interfaces;

interface OntologyRepositoryInterface
{

    /**
     * Fetch an Ontology by id
     *
     * @param integer $ontology_id
     *
     * @return array Ontology
     */
    public function getById($ontology_id);

    /**
     * Fetch all Ontology objects
     *
     * @return array of Ontology's
     */
    public function getAll();

    /**
     * Fetch a URI by its prefix
     *
     * @param  string $prefix
     *
     * @return string
     */
    public function getByPrefix($prefix);
}
