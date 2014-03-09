<?php

use Illuminate\Database\Migrations\Migration;

class InstalledResources extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table for the model.
        Schema::create('installeddefinitions', function($table){

            $table->increments('id');
            $table->string('class', 100);
            $table->string('path', 255);
            $table->string('description', 255);

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
        // Drop the table for the model.
        Schema::drop('installeddefinitions');
    }


}