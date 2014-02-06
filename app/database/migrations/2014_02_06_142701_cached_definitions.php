<?php

use Illuminate\Database\Migrations\Migration;

class CachedDefinitions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the dublin core to the definitions.
        Schema::table('definitions', function($table)
        {
            $table->smallInteger('cache_minutes')->default(5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the dublin core from the definitions.
        Schema::table('definitions', function($table)
        {
            $table->dropColumn('cache_minutes');
        });


    }
}