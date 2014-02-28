<?php

namespace repositories\interfaces;

interface OntologyRepositoryInterface{

    public function getById($id);
    public function getAll();
}