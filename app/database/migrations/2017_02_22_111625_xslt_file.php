<?php

use Illuminate\Database\Migrations\Migration;

class XsltFile extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('definitions', function($table)
		{
            $table->string('xslt_file', 255)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('definitions', function($table)
		{
            $table->dropColumn('xslt_file');

		});
	}

}
