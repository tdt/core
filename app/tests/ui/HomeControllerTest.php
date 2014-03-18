<?php

class HomeControllerTest extends TestCase
{

    /**
     * Test index
     */
    public function testIndex()
    {

        Artisan::call('db:seed', array('--class'=>'DemoDataSeeder'));
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseOk();

        $this->assertCount(1, $crawler->filter('h1:contains("Datasets")'));
        $this->assertCount(1, $crawler->filter('a:contains("csv/geo")'));
        $this->assertCount(1, $crawler->filter('div.note:contains("Shape file about rivers in Dresden.")'));
    }
}
