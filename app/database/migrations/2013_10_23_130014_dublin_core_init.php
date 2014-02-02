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
			$table->string('title')->nullable();
			$table->string('creator')->nullable();
			$table->string('subject')->nullable();
			$table->string('description')->nullable();
			$table->string('publisher')->nullable();
			$table->string('contributor')->nullable();
			$table->string('date')->nullable();
			$table->string('type')->nullable();
			$table->string('format')->nullable();
			$table->string('identifier')->nullable();
			$table->string('source')->nullable();
			$table->string('language')->nullable();
			$table->string('relation')->nullable();
			$table->string('coverage')->nullable();
			$table->string('rights')->nullable();

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
			/*$table->dropColumn('title');
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
			$table->dropColumn('rights');*/
		});


	}

}