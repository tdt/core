<?php

use Illuminate\Database\Migrations\Migration;

class NullableFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//  Alter varchar length to 255 instead of 256
		\DB::statement('ALTER TABLE sparqldefinitions MODIFY COLUMN endpoint_user VARCHAR(255) NULL');
		\DB::statement('ALTER TABLE sparqldefinitions MODIFY COLUMN endpoint_password VARCHAR(255) NULL');
		\DB::statement('ALTER TABLE sparqldefinitions MODIFY COLUMN query VARCHAR(255) NULL');
		\DB::statement('ALTER TABLE sparqldefinitions MODIFY COLUMN description VARCHAR(255) NULL');
		\DB::statement('ALTER TABLE sparqldefinitions MODIFY COLUMN endpoint VARCHAR(255) NULL');

		\DB::statement('ALTER TABLE sparqldefinitions MODIFY COLUMN description VARCHAR(255) NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// No rollback necessary since 256 is an illegal or non-accepted varchar size

	}

}