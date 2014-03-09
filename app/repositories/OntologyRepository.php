<?php

namespace repositories;

use repositories\interfaces\OntologyRepositoryInterface;

class OntologyRepository implements OntologyRepositoryInterface
{

    public function getById($ontology_id)
    {
        return \Ontology::find($ontology_id)->toArray();
    }

    public function getAll()
    {
        return \Ontology::all(array('prefix', 'uri'))->toArray();
    }
}
