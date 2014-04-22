<?php

namespace Tdt\Core\Tests\Ui;

class DatasetControllerAdminTest extends BaseUITest
{

    /**
     * Test redirect
     */
    public function testRedirect()
    {
        // var_dump();
        // var_dump(get_class($this->client));
        // die();
        $crawler = $this->client->request('GET', '/api/admin');
        $this->assertRedirectedTo('/api/admin/datasets');
    }

    /**
     * Test index
     */
    public function testIndex()
    {
        $crawler = $this->requestWithAuth('GET', '/api/admin/datasets');
        $this->assertResponseOk();

        $this->assertCount(1, $crawler->filter('h3:contains("Manage your data")'));
        $this->assertCount(1, $crawler->filter('li.active > a:contains("Datasets")'));
        $this->assertCount(1, $crawler->filter('h4 > a:contains("france/places")'));
    }

    public function testAdd()
    {
        // $crawler = $this->requestWithAuth('GET', '/api/admin/datasets/add');
        // $this->assertResponseOk();

        // $this->assertCount(1, $crawler->filter('h3:contains("Add a dataset")'));
        // $this->assertCount(1, $crawler->filter('h4:contains("Required parameters")'));
        // $this->assertCount(1, $crawler->filter('li.active > a:contains("Datasets")'));
    }
}
