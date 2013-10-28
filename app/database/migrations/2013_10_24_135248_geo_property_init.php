<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeoPropertyInit extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('geoproperties', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('source_type', '256');
			$table->integer('source_id');
			$table->string('path', 256);
			$table->string('geo_type', 256);

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
		Schema::drop('geoproperties');
	}

}
