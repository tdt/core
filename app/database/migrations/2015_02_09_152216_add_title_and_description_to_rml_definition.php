<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleAndDescriptionToRmlDefinition extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add title and description to the rml definitions table
        Schema::table('rmldefinitions', function ($table) {

            $table->string('description', 255);
            $table->string('title', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Undo the above changes
        Schema::table('rmldefinitions', function ($table) {

            $table->dropColumn('description');
            $table->dropColumn('title');
        });
    }
}
