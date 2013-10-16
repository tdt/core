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

}
