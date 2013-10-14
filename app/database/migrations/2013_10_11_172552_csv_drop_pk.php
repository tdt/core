<?php

use Illuminate\Database\Migrations\Migration;

class CsvDropPk extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Drop the column pk in the CSV definitions table
		Schema::table('csvdefinitions', function($table)
		{
		    $table->dropColumn('pk');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Add the column pk in the CSV definitions table.
		Schema::table('csvdefinitions', function($table)
		{
		    $table->string('pk');
		});
	}

}