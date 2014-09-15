<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DcatCatalog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the general settings table
		Schema::create('general_settings', function($table){

			$table->string('key', 255);
			$table->string('value', 255);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the general settings table
		Schema::drop('general_settings');
	}
}
