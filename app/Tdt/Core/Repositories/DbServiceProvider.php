<?php

namespace Tdt\Core\Repositories;

use Illuminate\Support\ServiceProvider;

class DbServiceProvider extends ServiceProvider
{
    public function register()
    {

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface',
            'Tdt\Core\Repositories\DefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\CsvDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\CsvDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface',
            'Tdt\Core\Repositories\TabularColumnsRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface',
            'Tdt\Core\Repositories\GeoPropertyRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\XlsDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\XlsDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\ShpDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\ShpDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\JsonDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\XmlDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\XmlDefinitionRepository'
        );


        \App::bind(
            'Tdt\Core\Repositories\Interfaces\SparqlDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\SparqlDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\InstalledDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\InstalledDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface',
            'Tdt\Core\Repositories\LanguageRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\LicenseRepositoryInterface',
            'Tdt\Core\Repositories\LicenseRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\OntologyRepositoryInterface',
            'Tdt\Core\Repositories\OntologyRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\RdfDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\RdfDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\JsonldDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\JsonldDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\DcatRepositoryInterface',
            'Tdt\Core\Repositories\DcatRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\SettingsRepositoryInterface',
            'Tdt\Core\Repositories\SettingsRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\ThemeRepositoryInterface',
            'Tdt\Core\Repositories\ThemeRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\MysqlDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\MysqlDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\MongoDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\MongoDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\ElasticsearchDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\ElasticsearchDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\GeoprojectionRepositoryInterface',
            'Tdt\Core\Repositories\GeoprojectionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\RemoteDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\RemoteDefinitionRepository'
        );

        \App::bind(
            'Tdt\Core\Repositories\Interfaces\InspireDefinitionRepositoryInterface',
            'Tdt\Core\Repositories\InspireDefinitionRepository'
        );
    }
}
