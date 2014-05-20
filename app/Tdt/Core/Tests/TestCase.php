<?php

namespace Tdt\Core\Tests;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{

    /**
     * Creates the application.
     * This function is automatically called by Laravel.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__ . '/../../../../bootstrap/start.php';
    }

    /**
     * Default preparation for each test
     */
    public function setUp()
    {

        parent::setUp();

        $this->prepareForTests();
    }

    /**
     * Prepare for the tests to be run.
     */
    public function prepareForTests()
    {

        // Enable your route filters, very important!
        \Route::enableFilters();
        \Route::any('{all}', 'Tdt\Core\BaseController@handleRequest')->where('all', '.*');
        \Mail::pretend(true);
    }

    /**
     * Delete everything out of our testing database.
     */
    public static function tearDownAfterClass()
    {

        parent::tearDownAfterClass();

        \Definition::truncate();
        \CsvDefinition::truncate();
        \InstalledDefinition::truncate();
        \JsonDefinition::truncate();
        // RdfDefinition::truncate();
        \ShpDefinition::truncate();
        \SparqlDefinition::truncate();
        \XlsDefinition::truncate();
        \XmlDefinition::truncate();
        \GeoProperty::truncate();
        \TabularColumns::truncate();
        \JsonldDefinition::truncate();
    }

    /**
     * Custom API call function
     */
    public function updateRequest($method, $headers = array(), $data = array())
    {

        // Log in as admin - header
        $headers['Authorization'] = 'Basic YWRtaW46YWRtaW4=';

        // Set the custom headers.
        \Request::getFacadeRoot()->headers->replace($headers);

        // Set the custom method.
        \Request::setMethod($method);

        // Set the content body.
        if (is_array($data)) {
            \Input::merge($data);
        }
    }
}
