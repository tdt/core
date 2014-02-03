<?php

use Illuminate\Database\Migrations\Migration;

class SparqlQueryToText extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Do not roll this back, query has to be text
	}

}