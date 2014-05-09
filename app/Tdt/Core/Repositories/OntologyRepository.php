<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\OntologyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function getByPrefix($prefix)
    {
        try {
            return \Ontology::where('prefix', $prefix)->firstOrFail()->toArray();
        } catch (ModelNotFoundException $ex) {
            return null;
        }
    }
}
