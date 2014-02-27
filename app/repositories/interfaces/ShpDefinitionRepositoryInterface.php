<?php

namespace repositories\interfaces;

interface ShpDefinitionRepositoryInterface{

    public function store($input);
    public function update($id, $input);
    public function delete($id);
}