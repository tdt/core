<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the themes table
		Schema::create('themes', function($table) {
			$table->increments('id');
			$table->string('uri', 255);
			$table->string('label', 255);
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
		//  Drop the themes table
		Schema::drop('themes');
	}
}
