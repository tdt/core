<?php

namespace repositories\interfaces;

interface TabularColumnsRepositoryInterface{

    public function store($input);
    public function delete($id);
}