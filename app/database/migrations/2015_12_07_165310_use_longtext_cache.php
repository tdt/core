<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UseLongtextCache extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Drop to get rid of any cache data as well.
		Schema::drop('cache');
		Schema::create('cache', function ($table) {
			$table->string('key')->unique();
			$table->longText('value');
			$table->integer('expiration');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop to get rid of any cache data as well.
		Schema::drop('cache');
		Schema::create('cache', function ($table) {
			$table->string('key')->unique();
			$table->text('value');
			$table->integer('expiration');
		});
	}

}
