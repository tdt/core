<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeodcatHarvester extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function ($table) {
            $table->increments('id');
            $table->integer('definition_id');
            $table->timestamps();
        });

        Schema::create('labels', function ($table) {
            $table->increments('id');
            $table->string('label', 255);
            $table->integer('location_id');
            $table->timestamps();
        });

        Schema::create('geometries', function ($table) {
            $table->increments('id');
            $table->string('type', 255);
            $table->text('geometry');
            $table->integer('location_id');
            $table->timestamps();
        });

        Schema::create('services', function ($table) {
            $table->increments('id');
            $table->string('uri', 255);
            $table->integer('definition_id');
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
        Schema::drop('locations');
        Schema::drop('labels');
        Schema::drop('geometries');
        Schema::drop('services');
    }
}
