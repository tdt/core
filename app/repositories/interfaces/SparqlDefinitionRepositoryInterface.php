<?php

namespace repositories\interfaces;

interface SparqlDefinitionRepositoryInterface{

    public function store($input);
    public function update($id, $input);
    public function delete($id);
}