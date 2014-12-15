<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TitleOnSourcetype extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the title on definitions

        Schema::table('definitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('csvdefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('installeddefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('jsondefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('jsonlddefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('rdfdefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('shpdefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('sparqldefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('xlsdefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });

        Schema::table('xmldefinitions', function ($table) {
            $table->string('title', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Re-roll changes
        Schema::table('definitions', function ($table) {
            $table->string('title', 255);
        });

        Schema::table('csvdefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('installeddefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('jsondefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('jsonlddefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('rdfdefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('shpdefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('sparqldefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('xlsdefinitions', function ($table) {
            $table->dropColumn('title');
        });

        Schema::table('xmldefinitions', function ($table) {
            $table->dropColumn('title');
        });

    }
}
