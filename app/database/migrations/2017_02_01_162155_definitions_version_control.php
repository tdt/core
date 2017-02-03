<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefinitionsVersionControl extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
		Schema::table('definitions', function($table)
		{
			$table->integer('user_id')->unsigned();
			$table->string('username', 255);
		});		
		
        Schema::create('definitions_updates', function ($table) {
            $table->increments('id');
			$table->integer('definition_id')->unsigned();
            $table->integer('user_id')->unsigned();
			$table->string('username', 255);
            $table->dateTime('updated_at');
			$table->tinyInteger('user_deleted')->unsigned()->nullable();
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
			$table->dropColumn(array('user_id', 'username'));
		});
		
		Schema::drop('definitions_updates');
	}

}
