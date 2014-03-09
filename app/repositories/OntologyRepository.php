<?php

namespace repositories;

use repositories\interfaces\OntologyRepositoryInterface;

class OntologyRepository implements OntologyRepositoryInterface
{

    public function getById($id)
    {
        return \Ontology::find($id)->toArray();
    }

    public function getAll()
    {
        return \Ontology::all(array('prefix', 'uri'))->toArray();
    }

}