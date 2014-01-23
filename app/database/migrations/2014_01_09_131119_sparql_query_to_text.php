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
		// Set the type of a sparql query to text, from varchar(255)
		\DB::statement('ALTER TABLE sparqldefinitions MODIFY COLUMN query TEXT NULL');
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