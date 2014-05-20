<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DisplayProperty extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add the map_property to the definitions table
		Schema::table('definitions', function($table){
			$table->string('map_property', 255)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the map_property from the definitions table
		Schema::table('definitions', function($table){
			$table->dropColumn('map_property');
		});
	}
}
