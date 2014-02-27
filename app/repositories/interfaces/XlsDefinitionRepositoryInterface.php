<?php

namespace repositories\interfaces;

interface XlsDefinitionRepositoryInterface{

    public function store($input);
    public function update($id, $input);
    public function delete($id);
}