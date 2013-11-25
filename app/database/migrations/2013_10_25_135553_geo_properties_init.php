<?php

use Illuminate\Database\Migrations\Migration;

class GeoPropertiesInit extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table for the Geoproperties model
        Schema::create('geoproperties', function($table){

            $table->increments('id');
            $table->string('source_type', 255);
            $table->integer('source_id');
            $table->string('path', 255);
            $table->string('geo_property', 255);

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
        // Drop the table for Geoproperties model
        Schema::drop('geoproperties');
    }
}
