<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSqlDefinition extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sqldefinitions', function ($table) {
            $table->increments('id');
            $table->string('host', 255);
            $table->integer('port');
            $table->string('username', 255);
            $table->string('password', 255);
            $table->string('database', 255);
            $table->text('query');
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
        Schema::drop('sqldefinitions');
    }
}
