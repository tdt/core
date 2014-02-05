<?php

use Illuminate\Database\Migrations\Migration;

class Initialize extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the definitions table
        Schema::create('definitions', function($table){

            $table->increments('id');
            $table->string('collection_uri', 255);
            $table->string('resource_name', 255);
            $table->string('source_type', 255);
            $table->integer('source_id');

            // created_at | updated_at DATETIME, are default expected by the Eloquent ORM
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
        // Drop the definitions table
        Schema::drop('definitions');
    }

}