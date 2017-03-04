<?php

use Illuminate\Database\Migrations\Migration;

class ChangeColumnNameMysqlResource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mysqldefinitions', function ($table) {
            $table->renameColumn('host', 'mysql_host');
            $table->renameColumn('password', 'mysql_password');
            $table->renameColumn('port', 'mysql_port');
            $table->renameColumn('username', 'mysql_username');
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
            $table->renameColumn('mysql_host', 'host');
            $table->renameColumn('mysql_password', 'password');
            $table->renameColumn('mysql_port', 'port');
            $table->renameColumn('mysql_username', 'username');
        });
    }
}
