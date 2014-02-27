<?php

namespace repositories\interfaces;

interface JsonDefinitionRepositoryInterface{

    public function store($input);
    public function update($id, $input);
    public function delete($id);
}