<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LinkJobDefinition extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('definitions', function($table)
		{
			$table->integer('job_id')->unsigned()->nullable();
            $table->integer('draft_flag')->unsigned()->nullable();
			$table->string('original_file', 255)->nullable();
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
			$table->dropColumn(array('job_id', 'original_file'));
		});
	}

}
