<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DraftDefinitions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Update the definitions table with a draft field
		Schema::table('definitions', function($table){
			$table->boolean('draft');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the draft column
		Schema::table('definitions', function($table){
			$table->dropColumn('draft');
		});
	}

}
