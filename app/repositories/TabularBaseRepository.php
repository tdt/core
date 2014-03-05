<?php

namespace repositories;

class TabularBaseRepository extends BaseDefinitionRepository{

    protected $tabular_repository;
    protected $geo_repository;

    public function __construct(){

        $this->tabular_repository = \App::make('repositories\interfaces\TabularColumnsRepositoryInterface');
        $this->geo_repository = \App::make('repositories\interfaces\GeoPropertyRepositoryInterface');
    }
}