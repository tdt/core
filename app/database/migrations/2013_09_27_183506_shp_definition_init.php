<?php

use Illuminate\Database\Migrations\Migration;

class ShpDefinitionInit extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the table for the SHPDefinition model.
		Schema::create('shpdefinitions', function($table){

            $table->increments('id');
            $table->string('uri', 255);
            $table->string('epsg', 25);
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
		// Drop the table for the SHPDefinition model.
		Schema::drop('shpdefinitions');
	}

}