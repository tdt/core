<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MysqlResource extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table for the mysql resource
        Schema::create('mysqldefinitions', function ($table) {

            $table->increments('id');
            $table->string('host', 255);
            $table->string('port', 255);
            $table->string('database', 255);
            $table->string('datatable', 255);
            $table->string('username', 255);
            $table->string('password', 255);
            $table->string('collation', 255);
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
        // Drop the mysql resource table
        Schema::drop('mysqldefinitions');
    }
}
