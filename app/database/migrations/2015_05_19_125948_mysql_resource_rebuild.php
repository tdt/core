<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MysqlResourceRebuild extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add query to the mysql source definition table
        Schema::table('mysqldefinitions', function ($table) {
            $table->text('query');
            $table->dropColumn('datatable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mysqldefinitions', function ($table) {
            $table->dropColumn('query');
            $table->string('datatable', 255);
        });
    }
}
