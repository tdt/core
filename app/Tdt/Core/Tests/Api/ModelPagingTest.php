<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Definitions\InfoController;
use Tdt\Core\Definitions\DcatController;

use Symfony\Component\HttpFoundation\Request;

class ModelPagingTest extends TestCase
{

    // Add csv files only, the goal is to seed the database with some definitions.
    private $test_data = array(
                'comma_in_quotes',
                'escaped_quotes',
                'json',
                'latin1',
                'newlines',
                'quotes_and_newlines',
                'simple',
                'utf8',
            );

    public function testPutApi()
    {

        // Add the CSV definitions
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $data = array(
                'description' => "A CSV publication from the $file csv file.",
                'delimiter' => ',',
                'uri' => app_path() . "/storage/data/tests/csv/$file.csv",
                'type' => 'csv'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            // Put the definition controller to the test!
            $response = $controller->handle("csv/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    /*
     * Test the model through the variety of possibilities we offer our
     * internal model to the public
     *
     * Note: don't name the private functions testXXX, this will result in
     * phpunit warnings as it will try to use it as a proper testcase.
     */
    public function testGetApi()
    {

        // Set paging to 2
        \Input::merge(array('limit' => 2));

        // Test the internal model through info
        $controller = $controller = \App::make('Tdt\Core\Definitions\InfoController');
        $this->processPaging($controller->handle(''));

        // Test the internal model through definitions
        $this->updateRequest('GET');

        $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

        $this->processPaging($controller->handle(''));

        // Test the internal model through dcat
        $this->processDcat();
    }

    public function testDeleteApi()
    {

        // Delete the published definition for each test csv file.
        foreach ($this->test_data as $file) {

            $this->updateRequest('DELETE');

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("csv/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = \Definition::all()->count();
        $csv_count = \CsvDefinition::all()->count();

        $this->assertTrue($csv_count == 0);
        $this->assertTrue($definitions_count == 0);
    }

    private function processPaging($response)
    {

         // The body should have only 2 entries
        $body = json_decode($response->getOriginalContent(), true);

        $this->assertEquals(count($body), 2);

        $this->processLinkHeader($response);
    }

    private function processLinkHeader($response)
    {

        // The link headers should be properly set
        $link_header = $response->headers->get('Link');

        $links = explode(',', $link_header);

        // Check for link to next and last
        // Don't let the order of these links be a hassle
        foreach ($links as $link) {

            if (preg_match('/(.*)\?(limit|offset)=(\d+)&(limit|offset)=(\d+);rel=(.*)/', $link, $matches)) {

                if ($matches[6] == 'last') {

                    if ($matches[2] == 'offset') {
                        $this->assertEquals($matches[3], 6);
                    } else {
                        $this->assertEquals($matches[5], 2);
                    }
                } else {

                    if ($matches[2] == 'offset') {
                        $this->assertEquals($matches[3], 2);
                    } else {
                        $this->assertEquals($matches[5], 2);
                    }
                }
            }
        }
    }

    private function processDcat()
    {

        $controller = \App::make('Tdt\Core\Definitions\DcatController');

        $response = $controller->handle('');

        // Get the semantic (turtle) content
        $turtle = $response->getOriginalContent();

        $graph = new \EasyRdf_Graph();
        $parser = new \EasyRdf_Parser_Turtle();

        $total_triples = $parser->parse($graph, $turtle, 'turtle', '');

        // Make sure triples are created and inserted into the graph
        $this->assertEquals(14, $total_triples);

        // This array will hold 3 entries, one for the Dcat catalog entry itself
        // and the two first entries of the definitions it gets from the controller
        $dcat_array = $graph->toRdfPhp();

        $this->assertEquals(count($dcat_array), 3);

        $this->processLinkHeader($response);
    }
}
