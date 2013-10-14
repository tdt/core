<?php

use Illuminate\Database\Migrations\Migration;

class RemodelDocumentation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Remove the documentation column from Definition and
		// add it to the other models.
		Schema::table('definitions', function($table)
		{
		    $table->dropColumn('documentation');
		});

		Schema::table('csvdefinitions', function($table)
		{
		    $table->string('documentation');
		});

		Schema::table('shpdefinitions', function($table)
		{
		    $table->string('documentation');
		});

		Schema::table('jsondefinitions', function($table)
		{
		    $table->string('documentation');
		});

		Schema::table('xlsdefinitions', function($table)
		{
		    $table->string('documentation');
		});

		Schema::table('xmldefinitions', function($table)
		{
		    $table->string('documentation');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Add the column to the definitions table.
		Schema::table('definitions', function($table)
		{
		    $table->string('documentation');
		});

		Schema::table('csvdefinitions', function($table)
		{
		    $table->dropColumn('documentation');
		});

		Schema::table('shpdefinitions', function($table)
		{
		    $table->dropColumn('documentation');
		});

		Schema::table('jsondefinitions', function($table)
		{
		    $table->dropColumn('documentation');
		});

		Schema::table('xlsdefinitions', function($table)
		{
		    $table->dropColumn('documentation');
		});

		Schema::table('xmldefinitions', function($table)
		{
		    $table->dropColumn('documentation');
		});

	}

}