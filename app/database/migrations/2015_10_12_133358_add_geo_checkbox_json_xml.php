<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGeoCheckboxJsonXml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jsondefinitions', function ($table) {
            $table->boolean('geo_formatted')->nullable();
        });

        Schema::table('xmldefinitions', function ($table) {
            $table->boolean('geo_formatted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jsondefinitions', function ($table) {
            $table->dropColumn('geo_formatted');
        });

        Schema::table('xmldefinitions', function ($table) {
            $table->dropColumn('geo_formatted');
        });
    }
}
