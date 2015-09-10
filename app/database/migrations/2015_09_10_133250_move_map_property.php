<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveMapProperty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the title on definitions
        Schema::table('definitions', function ($table) {
            $table->dropColumn('map_property');
        });

        Schema::table('csvdefinitions', function ($table) {
            $table->string('map_property', 255)->nullable();
        });

        Schema::table('shpdefinitions', function ($table) {
            $table->string('map_property', 255)->nullable();
        });

        Schema::table('xlsdefinitions', function ($table) {
            $table->string('map_property', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the title on definitions
        Schema::table('definitions', function ($table) {
            $table->string('map_property', 255)->nullable();
        });

        Schema::table('csvdefinitions', function ($table) {
            $table->dropColumn('map_property');
        });

        Schema::table('shpdefinitions', function ($table) {
            $table->dropColumn('map_property');
        });

        Schema::table('xlsdefinitions', function ($table) {
            $table->dropColumn('map_property');
        });
    }
}
