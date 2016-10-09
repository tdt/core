<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalCsvFileToEsDatasource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('elasticsearchdefinitions', function ($table) {
            $table->string('original_file', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elasticsearchdefinitions', function ($table) {
            $table->dropColumn('original_file');
        });
    }
}
