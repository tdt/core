<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;

class DiscoveryTest extends TestCase
{

    public function testDiscovery()
    {
        $discovery_controller = \App::make('Tdt\Core\Definitions\DiscoveryController');

        $document = $discovery_controller->createDiscoveryDocument();

        $this->assertNotNull($document->version);
        $this->assertNotNull($document->protocol);
        $this->assertNotNull($document->rootUrl);
        $this->assertNotNull($document->resources->definitions);
        $this->assertNotNull($document->resources->info);
        $this->assertNotNull($document->resources->dcat);
        $this->assertNotNull($document->resources->languages);
        $this->assertNotNull($document->resources->licenses);
        $this->assertNotNull($document->resources->prefixes);

        // Small test to if every source reader is present
        $this->assertNotNull($document->resources->definitions->methods->put->body->csv);
        $this->assertNotNull($document->resources->definitions->methods->put->body->xml);
        $this->assertNotNull($document->resources->definitions->methods->put->body->json);
        $this->assertNotNull($document->resources->definitions->methods->put->body->shp);
        $this->assertNotNull($document->resources->definitions->methods->put->body->xls);
        $this->assertNotNull($document->resources->definitions->methods->put->body->jsonld);
        $this->assertNotNull($document->resources->definitions->methods->put->body->sparql);
        $this->assertNotNull($document->resources->definitions->methods->put->body->rdf);
    }
}
