<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run(){

		Eloquent::unguard();

		// Call the definition seeder
		$this->call('DefinitionSeeder');
	}

}