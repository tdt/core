<?php

use Illuminate\Database\Migrations\Migration;

class DublinCoreInit extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		 // Create the table for the dublin core model.
        Schema::create('dublincore', function($table){

        	$table->string('title');
			$table->string('creator');
			$table->string('subject');
			$table->string('description');
			$table->string('publisher');
			$table->string('contributor');
			$table->string('date');
			$table->string('type');
			$table->string('format');
			$table->string('identifier');
			$table->string('source');
			$table->string('language');
			$table->string('relation');
			$table->string('coverage');
			$table->string('rights');

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
		// Drop the table for the dublin core model.
        Schema::drop('dublincore');
	}

}