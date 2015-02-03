<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RmlResource extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create a table for an rml definition
		Schema::create('rmldefinitions', function ($table) {

			$table->increments('id');
			$table->string('mapping_document', 255);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Undo the above changes
		Schema::drop('rmldefinitions');
	}
}
