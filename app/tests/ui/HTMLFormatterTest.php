<?php

namespace Tdt\Core\Tests\Ui;

class HTMLFormatterTEst extends BaseUITest
{

    /**
     * Test tabular view
     */
    public function testTabularDataset()
    {
        $crawler = $this->client->request('GET', '/csv/geo');
        $this->assertResponseOk();

        $this->assertCount(1, $crawler->filter('h1:contains("csv/geo")'));
        $this->assertCount(1, $crawler->filter('table'));
        $this->assertCount(1, $crawler->filter('td:contains("Pusht Rod")'));
    }

    /**
     * Test code view
     */
    public function testCodeView()
    {
        $crawler = $this->client->request('GET', '/json/crime');
        $this->assertResponseOk();

        $this->assertCount(1, $crawler->filter('h1:contains("json/crime")'));
        $this->assertCount(1, $crawler->filter('pre.prettyprint'));
    }

    /**
     * Test map view
     */
    public function testMapView()
    {
        $crawler = $this->client->request('GET', '/dresden/rivers');
        $this->assertResponseOk();

        $this->assertCount(1, $crawler->filter('iframe'));
        $this->assertCount(1, $crawler->filter('div.map-container'));
    }
}
