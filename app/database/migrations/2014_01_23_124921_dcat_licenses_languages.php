<?php

use Illuminate\Database\Migrations\Migration;

class DcatLicensesLanguages extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the license and language table
        Schema::create('languages', function($table){

            // Id, 3 character code
            $table->string('lang_id', 3);
            $table->string('lang_code', 2);
            $table->string('name', 255);
            $table->timestamps();
        });

        Schema::create('licenses', function($table){

            $table->increments('id');
            $table->boolean('domain_content');
            $table->boolean('domain_data');
            $table->boolean('domain_software');
            $table->string('family', 255)->nullable();
            $table->string('license_id', 255);
            $table->boolean('is_generic')->nullable();
            $table->boolean('is_okd_compliant');
            $table->boolean('is_osi_compliant');
            $table->string('maintainer', 255)->nullable();
            $table->string('status', 255);
            $table->string('title', 255);
            $table->string('url', 255);
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
        // Drop the licenses and languages table
        Schema::drop('licenses');
        Schema::drop('languages');
    }

}