<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MergeJsonTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('jsonlddefinitions');
        Schema::table('jsondefinitions', function ($table) {
            $table->dropColumn('geo_formatted');
            $table->string('jsontype', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('jsonlddefinitions', function ($table) {
            $table->increments('id');
            $table->string('uri', 255);
            $table->string('description', 255);
            $table->timestamps();
        });

        Schema::table('jsondefinitions', function ($table) {
            $table->dropColumn('jsontype');
            $table->boolean('geo_formatted')->nullable();
        });
    }
}
