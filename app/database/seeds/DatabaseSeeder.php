<?php

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Call the user seeder
        $this->call('UserSeeder');

        // Call the DcatSeeder
        $this->call('DcatSeeder');

        // Call the OntologyPrefixSeeder
        $this->call('OntologyPrefixSeeder');

        $this->class('GeoSeeder');
    }
}
