<?php

use Illuminate\Database\Migrations\Migration;

class XlsDefinition extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the XLS definition table.
		Schema::create('xlsdefinitions', function($table){

            $table->increments('id');
            $table->string('uri', 255);
            $table->string('sheet', 255);
            $table->boolean('has_header_row');
            $table->integer('start_row');

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
		// Drop the XLS definition table.
		Schema::drop('xlsdefinition');
	}

}