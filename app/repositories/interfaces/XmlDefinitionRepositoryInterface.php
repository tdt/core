<?php

namespace repositories\interfaces;

interface XmlDefinitionRepositoryInterface{

    public function store($input);
    public function update($id, $input);
    public function delete($id);
}