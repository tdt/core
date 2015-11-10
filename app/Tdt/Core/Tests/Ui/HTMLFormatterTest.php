<?php

namespace Tdt\Core\Tests\Ui;

class HTMLFormatterTest extends BaseUITest
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
        \Geoprojection::create([
            'epsg' => 4326,
            'projection' => "GEOGCS[\"WGS 84\",DATUM[\"WGS_1984\",SPHEROID[\"WGS 84\",6378137,298.257223563,AUTHORITY[\"EPSG\",\"7030\"]],AUTHORITY[\"EPSG\",\"6326\"]],PRIMEM[\"Greenwich\",0,AUTHORITY[\"EPSG\",\"8901\"]],UNIT[\"degree\",0.01745329251994328,AUTHORITY[\"EPSG\",\"9122\"]],AUTHORITY[\"EPSG\",\"4326\"]]"
        ]);

        $crawler = $this->client->request('GET', '/dresden/rivers');
        $this->assertResponseOk();

        $this->assertCount(1, $crawler->filter('iframe'));
        $this->assertCount(1, $crawler->filter('div.map-container'));
    }
}
