<?php

namespace repositories\interfaces;

interface OntologyRepositoryInterface{

    /**
     * Fetch an Ontology by id
     *
     * @param integer $id
     * @return array Ontology
     */
    public function getById($id);

    /**
     * Fetch all Ontology objects
     *
     * @return array of Ontology's
     */
    public function getAll();
}