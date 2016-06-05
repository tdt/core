<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributionsToDefinition extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributions', function ($table) {
            $table->increments('id');
            $table->string('role', 255);
            $table->string('email', 255);
            $table->string('name', 255);
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
        Schema::drop('attributions');
    }
}
