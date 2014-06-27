<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitRdfDefinition extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create a table for the rdf definition
        Schema::create('rdfdefinitions', function($table){

            $table->increments('id');
            $table->string('uri', 255);
            $table->string('description', 255);
            $table->string('format', 255);
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
        // Drop the table for the rdf definition
        Schema::drop('rdfdefinitions');
    }
}
