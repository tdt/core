<?php

namespace Tdt\Core\Tests\Ui;

class HomeControllerTest extends BaseUITest
{

    /**
     * Test index
     */
    public function testIndex()
    {
        \Artisan::call('db:seed', array('--class'=>'DemoDataSeeder'));
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseOk();
    }
}
