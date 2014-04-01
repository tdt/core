<?php

use Illuminate\Database\Migrations\Migration;

class XmlDefinitionInit extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the xml definition table
		Schema::create('xmldefinitions', function($table){

            $table->increments('id');
            $table->string('uri', 255);
            $table->string('description', 1024);

            // created_at | updated_at DATETIME, are default expected by the Eloquent ORM.
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
		// Drop the xml definition table
		Schema::drop('xmldefinitions');
	}
}
