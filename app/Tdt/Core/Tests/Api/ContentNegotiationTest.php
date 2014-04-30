<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\ContentNegotiator;

class ContentNegotiationTest extends TestCase
{

    public function testJson()
    {
        $response = $this->makeRequest(array('Accept' => 'application/json'));

        $content_type = $response->headers->get('Content-Type');

        $this->assertEquals('application/json;charset=UTF-8', $content_type);
    }

    public function testXml()
    {
        $response = $this->makeRequest(array('Accept' => 'application/xml'));

        $content_type = $response->headers->get('Content-Type');

        $this->assertEquals('text/xml;charset=UTF-8', $content_type);
    }

    public function testJsonld()
    {
        $response = $this->makeRequest(array('Accept' => 'application/ld+json'), true);

        $content_type = $response->headers->get('Content-Type');

        $this->assertEquals('application/ld+json;charset=UTF-8', $content_type);
    }

    public function testTurtle()
    {
        $response = $this->makeRequest(array('Accept' => 'text/turtle'), true);

        $content_type = $response->headers->get('Content-Type');

        $this->assertEquals('text/turtle;charset=UTF-8', $content_type);
    }

    public function testNTriples()
    {
        $response = $this->makeRequest(array('Accept' => 'application/n-triples'), true);

        $content_type = $response->headers->get('Content-Type');

        $this->assertEquals('application/n-triples;charset=UTF-8', $content_type);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testNoFormatSupported()
    {
        $response = $this->makeRequest(array('Accept' => '*/*;q=0.0'));
    }

    private function makeRequest(array $accept_header, $semantic = false)
    {
        $data = \Mockery::mock('Tdt\Core\Datasets\Data');

        if ($semantic) {
            $data->data = new \EasyRdf_Graph();
        } else {
            $data->data = array();
        }

        $data->is_semantic = $semantic;

        $this->updateRequest('GET', $accept_header);

        return ContentNegotiator::getResponse($data, null);
    }
}
