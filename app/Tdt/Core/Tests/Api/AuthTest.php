<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * @expectedException ErrorException
     */
    public function testPutDefinition()
    {
        $definitions = \App::make('Tdt\Core\Definitions\DefinitionController');

        $reponse = $definitions->put('foo');
    }

    /**
     * @expectedException ErrorException
     */
    public function testGetDefinition()
    {
        $definitions = \App::make('Tdt\Core\Definitions\DefinitionController');

        $reponse = $definitions->get('foo');
    }

    /**
     * @expectedException ErrorException
     */
    public function testDeleteDefinition()
    {
        $definitions = \App::make('Tdt\Core\Definitions\DefinitionController');

        $reponse = $definitions->delete('foo');
    }

    /**
     * @expectedException ErrorException
     */
    public function testPostDefinition()
    {
        $definitions = \App::make('Tdt\Core\Definitions\DefinitionController');

        $reponse = $definitions->patch('foo');
    }

    /**
     * @expectedException ErrorException
     */
    public function testHeadDefinition()
    {
        $definitions = \App::make('Tdt\Core\Definitions\DefinitionController');

        $reponse = $definitions->head('foo');
    }
}
