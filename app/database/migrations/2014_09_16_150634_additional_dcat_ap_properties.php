<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdditionalDcatApProperties extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add the keywords, publisher_uri and publisher_name to the definition table
		Schema::table('definitions', function($table){
			$table->string('keywords', 255)->nullable();
			$table->string('publisher_uri', 255)->nullable();
			$table->string('publisher_name', 255)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the added columns from the definition table
		Schema::table('definitions', function($table){
			$table->dropColumn('keywords');
			$table->dropColumn('publisher_name');
			$table->dropColumn('publisher_uri');
		});
	}
}
