<?php

namespace Tdt\Core\Tests\Ui;

use Tdt\Core\Tests\TestCase;

abstract class BaseUITest extends TestCase
{

    protected $headers = array(
        "HTTP_Authorization" => "Basic YWRtaW46YWRtaW4=",
    );

    /**
     * Seed the demo data for UI tests
     */
    public function prepareForTests()
    {
        parent::prepareForTests();
        \Artisan::call('db:seed', array('--class'=>'DemoDataSeeder'));
    }

    /**
     * Request with Authorization header
     */
    public function requestWithAuth($method, $uri)
    {
        return $this->client->request($method, $uri, array(), array(), $this->headers);
    }
}
