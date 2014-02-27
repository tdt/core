<?php

namespace repositories\interfaces;

interface CsvDefinitionRepositoryInterface{

    public function store($input);
    public function update($id, $input);
    public function delete($id);
}