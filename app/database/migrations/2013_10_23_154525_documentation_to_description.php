<?php

use Illuminate\Database\Migrations\Migration;

class DocumentationToDescription extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Rename the documentation column to description
		Schema::table('csvdefinitions', function($table)
		{
		    $table->renameColumn('documentation', 'description');
		});

		Schema::table('shpdefinitions', function($table)
		{
		    $table->renameColumn('documentation', 'description');
		});

		Schema::table('jsondefinitions', function($table)
		{
		    $table->renameColumn('documentation', 'description');
		});

		Schema::table('xlsdefinitions', function($table)
		{
		    $table->renameColumn('documentation', 'description');
		});

		Schema::table('xmldefinitions', function($table)
		{
		    $table->renameColumn('documentation', 'description');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Rename the description column to documentation column
		Schema::table('csvdefinitions', function($table)
		{
		    $table->renameColumn('description', 'documentation');
		});

		Schema::table('shpdefinitions', function($table)
		{
		    $table->renameColumn('description', 'documentation');
		});

		Schema::table('jsondefinitions', function($table)
		{
		    $table->renameColumn('description', 'documentation');
		});

		Schema::table('xlsdefinitions', function($table)
		{
		    $table->renameColumn('description', 'documentation');
		});

		Schema::table('xmldefinitions', function($table)
		{
		    $table->renameColumn('description', 'documentation');
		});
	}
}