<?php

use Illuminate\Database\Migrations\Migration;

class JsonDefinitionInit extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table for the JSONDefinition model.
        Schema::create('jsondefinitions', function($table){

            $table->increments('id');
            $table->string('uri', 255);

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
        // Drop the table for the JSONDefinition model.
        Schema::drop('jsondefinitions');
    }

}