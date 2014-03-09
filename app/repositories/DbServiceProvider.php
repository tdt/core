<?php

namespace repositories;

use Illuminate\Support\ServiceProvider;

class DbServiceProvider extends ServiceProvider
{

    public function register()
    {

        \App::bind(
            'repositories\interfaces\DefinitionRepositoryInterface',
            'repositories\DefinitionRepository'
            );

        \App::bind(
            'repositories\interfaces\CsvDefinitionRepositoryInterface',
            'repositories\CsvDefinitionRepository'
            );

        \App::bind(
            'repositories\interfaces\TabularColumnsRepositoryInterface',
            'repositories\TabularColumnsRepository'
            );

        \App::bind(
            'repositories\interfaces\GeoPropertyRepositoryInterface',
            'repositories\GeoPropertyRepository'
            );

        \App::bind(
            'repositories\interfaces\XlsDefinitionRepositoryInterface',
            'repositories\XlsDefinitionRepository'
            );

        \App::bind(
            'repositories\interfaces\ShpDefinitionRepositoryInterface',
            'repositories\ShpDefinitionRepository'
            );

        \App::bind(
            'repositories\interfaces\JsonDefinitionRepositoryInterface',
            'repositories\JsonDefinitionRepository'
            );

        \App::bind(
            'repositories\interfaces\XmlDefinitionRepositoryInterface',
            'repositories\XmlDefinitionRepository'
            );


        \App::bind(
            'repositories\interfaces\SparqlDefinitionRepositoryInterface',
            'repositories\SparqlDefinitionRepository'
            );

        \App::bind(
            'repositories\interfaces\InstalledDefinitionRepositoryInterface',
            'repositories\InstalledDefinitionRepository'
            );

        \App::bind(
            'repositories\interfaces\LanguageRepositoryInterface',
            'repositories\LanguageRepository'
            );

        \App::bind(
            'repositories\interfaces\LicenseRepositoryInterface',
            'repositories\LicenseRepository'
            );

        \App::bind(
            'repositories\interfaces\OntologyRepositoryInterface',
            'repositories\OntologyRepository'
            );
    }
}