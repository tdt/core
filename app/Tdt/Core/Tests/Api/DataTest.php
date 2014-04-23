<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\ContentNegotiator;

class DataTest extends TestCase
{
    /**
     * Check if the link template header is filled in correctly
     */
    public function testLinkTemplate()
    {
        $data = \Mockery::mock('Tdt\Core\Datasets\Data');

        \Mockery::mock('Eloquent');

        $data->data = array();
        $data->optional_parameters = array('param1', 'param2');

        $response = ContentNegotiator::getResponse($data, 'json');

        $this->assertNotNull($response);

        $header_bag = $response->headers;

        $this->assertNotEmpty($header_bag->get('Link-Template'));
        $this->assertEquals('http://localhost.json{?param1, param2}', $header_bag->get('Link-Template'));

        $data->optional_parameters = array();
        $response = ContentNegotiator::getResponse($data, 'json');

        $header_bag = $response->headers;

        $this->assertNotNull($response);
        $this->assertEmpty($header_bag->get('Link-Template'));
    }
}
