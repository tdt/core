<?php

use Illuminate\Database\Migrations\Migration;

class RenameGeoProperty extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Rename the geo column geo_property to property
		Schema::table('geoproperties', function($table)
		{
			$table->renameColumn('geo_property', 'property');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Rollback the renaming
		Schema::table('geoproperties', function($table)
		{
			$table->renameColumn('property', 'geo_property');
		});
	}

}