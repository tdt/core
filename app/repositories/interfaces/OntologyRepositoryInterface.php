<?php

namespace repositories\interfaces;

interface OntologyRepositoryInterface
{

    /**
     * Fetch an Ontology by id
     *
     * @param integer $ontology_id
     * @return array Ontology
     */
    public function getById($ontology_id);

    /**
     * Fetch all Ontology objects
     *
     * @return array Ontology
     */
    public function getAll();
}
