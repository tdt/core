<?php

use Illuminate\Database\Migrations\Migration;

class TableColumns extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the table for the tabular columns model.
        Schema::create('tabularcolumns', function($table){

            $table->increments('id');
            $table->integer('index');
            $table->string('column_name', 255);
            $table->boolean('is_pk');
            $table->string('column_name_alias');
            $table->string('tabular_type');
            $table->integer('tabular_id');

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
		// Drop the tabular columns table.
		Schema::drop('tabularcolumns');
	}
}
