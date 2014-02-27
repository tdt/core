<?php

namespace repositories\interfaces;

interface GeoPropertyRepositoryInterface{

    public function store($input);
    public function delete($id);
}