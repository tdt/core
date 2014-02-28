<?php

namespace repositories\interfaces;

interface DefinitionRepositoryInterface{

    public function store($input);
    public function update($identifier, $input);
    public function delete($identifier);
    public function exists($identifier);
    public function getAll($limit, $offset);
    public function getByIdentifier($identifier);
    public function getByCollection($collection);
    public function getOldest();
    public function count();
    public function countPublished();

}