<?php

namespace repositories\interfaces;

interface InstalledDefinitionRepositoryInterface{

    public function store($input);
    public function update($id, $input);
    public function delete($id);
}