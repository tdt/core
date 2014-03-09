<?php

use Illuminate\Database\Migrations\Migration;

class OntologyPrefixes extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the table to store ontology URI's and corresponding prefixes
		Schema::create('ontologies', function($table){
			$table->string('prefix', 255);
			$table->string('uri', 255);
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
		// Drop the ontologies table
		Schema::drop('ontologies');
	}
}
