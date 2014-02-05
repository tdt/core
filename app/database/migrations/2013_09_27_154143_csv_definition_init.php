<?php

use Illuminate\Database\Migrations\Migration;

class CsvDefinitionInit extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table for the CSVDefinition model
        Schema::create('csvdefinitions', function($table){

            $table->increments('id');
            $table->string('delimiter', 5);
            $table->string('uri', 255);
            $table->boolean('has_header_row');
            $table->string('pk');
            $table->integer('start_row');
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
        // Drop the table for CSVDefinition model
        Schema::drop('csvdefinitions');
    }

}