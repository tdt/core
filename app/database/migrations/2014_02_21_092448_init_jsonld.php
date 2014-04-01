<?php

use Illuminate\Database\Migrations\Migration;

class InitJsonld extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the table for the JSON-LD definition
		Schema::create('jsonlddefinitions', function($table){

			$table->increments('id');
			$table->string('uri', 255);
			$table->string('description', 255);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the table
		Schema::drop('jsonlddefinitions');
	}
}
