<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkDefinitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('linked_definitions', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('linked_to')->unsigned()->index();
			$table->integer('linked_from')->unsigned()->index();
			$table->foreign('linked_to')->references('id')->on('definitions')->onDelete('cascade');
			$table->foreign('linked_from')->references('id')->on('definitions')->onDelete('cascade');
			
			$table->string('title_to', 255)->nullable();
			$table->string('title_from', 255)->nullable();
			$table->string('description', 255)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('linked_definitions');
	}

}
