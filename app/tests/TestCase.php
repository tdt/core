<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

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

		return require __DIR__.'/../../bootstrap/start.php';
	}

	/**
	 * Default preparation for each test
	 */
	public function setUp(){

		parent::setUp();

		$this->prepareForTests();
	}

	/**
	 * Prepare for the tests to be run.
	 */
	public function prepareForTests(){

        // Enable your route filters, very important!
        Route::enableFilters();
        Route::any('{all}', 'tdt\core\BaseController@handleRequest')->where('all', '.*');
        Artisan::call('migrate');
		Mail::pretend(true);
	}

	/**
	 * Delete everything out of our testing database.
	 */
	public static function tearDownAfterClass(){

		parent::tearDownAfterClass();
		Artisan::call('migrate:reset');
	}

	/**
	 * Custom API call function
	 */
	public function updateRequest($method, $headers = array(), $data = array()){

		// Set the custom headers.
		foreach($headers as $key => $value){

			// Prepare the request with the content-type header.
	        \Request::getFacadeRoot()->headers->replace([$key => $value]);
		}

        // Set the custom method.
        \Request::setMethod($method);

        // Set the content body.
        if(is_array($data)){
        	\Input::merge($data);
        }
	}
}
