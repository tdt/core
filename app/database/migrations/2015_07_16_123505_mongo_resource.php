<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MongoResource extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mongodefinitions', function ($table) {
            $table->increments('id');
            $table->string('mongo_collection', 255);
            $table->string('database', 255);
            $table->string('username', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->integer('port');
            $table->string('host', 255);
            $table->text('description');
            $table->string('title', 255)->nullable();
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
        Schema::drop('mongodefinitions');
    }
}
