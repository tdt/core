<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EsReader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elasticsearchdefinitions', function ($table) {
            $table->increments('id', true);
            $table->string('host', 255);
            $table->integer('port');
            $table->string('es_index', 255);
            $table->string('es_type', 255);
            $table->string('username', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->text('description');
            $table->string('title', 255);
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
        Schema::drop('elasticsearchdefinitions');
    }
}
