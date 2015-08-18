<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustLicenses extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licenses', function ($table) {
            $table->dropColumn('domain_content');
            $table->dropColumn('domain_data');
            $table->dropColumn('domain_software');
            $table->dropColumn('family');
            $table->dropColumn('is_generic');
            $table->dropColumn('is_okd_compliant');
            $table->dropColumn('is_osi_compliant');
            $table->dropColumn('maintainer');
            $table->dropColumn('status');
            $table->dropColumn('url');
        });

        Schema::table('licenses', function ($table) {
            $table->string('url', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licenses', function ($table) {
            $table->boolean('domain_content');
            $table->boolean('domain_data');
            $table->boolean('domain_software');
            $table->string('family', 255)->nullable();
            $table->boolean('is_generic')->nullable();
            $table->boolean('is_okd_compliant');
            $table->boolean('is_osi_compliant');
            $table->string('maintainer', 255)->nullable();
            $table->string('status', 255);
        });
    }
}
