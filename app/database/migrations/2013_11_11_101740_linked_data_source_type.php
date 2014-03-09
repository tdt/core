<?php

use Illuminate\Database\Migrations\Migration;

class LinkedDataSourceType extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the lddefinitions table for the LdDefinition model
		Schema::create('lddefinitions', function($table){

			$table->increments('id');
			$table->string('endpoint', 255);
			$table->string('endpoint_user', 255);
			$table->string('endpoint_password', 255);
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
		// Drop the lddefinitions table
		Schema::drop('lddefinitions');
	}

}
