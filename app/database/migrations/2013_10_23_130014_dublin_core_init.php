<?php

use Illuminate\Database\Migrations\Migration;

class DublinCoreInit extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add the dublin core to the definitions.
		Schema::table('definitions', function($table)
		{
			$table->string('title');
			$table->string('creator');
			$table->string('subject');
			$table->string('description');
			$table->string('publisher');
			$table->string('contributor');
			$table->string('date');
			$table->string('type');
			$table->string('format');
			$table->string('identifier');
			$table->string('source');
			$table->string('language');
			$table->string('relation');
			$table->string('coverage');
			$table->string('rights');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Remove the dublin core from the definitions.
		Schema::table('definitions', function($table)
		{
			$table->dropColumn('title');
			$table->dropColumn('creator');
			$table->dropColumn('subject');
			$table->dropColumn('description');
			$table->dropColumn('publisher');
			$table->dropColumn('contributor');
			$table->dropColumn('date');
			$table->dropColumn('type');
			$table->dropColumn('format');
			$table->dropColumn('identifier');
			$table->dropColumn('source');
			$table->dropColumn('language');
			$table->dropColumn('relation');
			$table->dropColumn('coverage');
			$table->dropColumn('rights');
		});


	}

}