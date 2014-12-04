<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThemeToDefinitions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add the theme column to the definitions table
		Schema::table('definitions', function($table){
			$table->string('theme', 255)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Remove the column from the definitions table
		Schema::table('definitions', function($table){
			$table->dropColumn('theme');
		});
	}
}
