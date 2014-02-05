<?php

class OntologyPrefixSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Eloquent::unguard();

        // Seed the database with ontology prefixes
        $this->seedPrefixes();
    }

    /**
     * Seed the prefixes table based on the prefixes json file
     *
     * @return void
     */
    private function seedPrefixes()
    {
        // Empty the ontology table
        \Ontology::truncate();

        // Fetch the ontology from the json file
        $prefixes = json_decode(file_get_contents(app_path() . '/database/seeds/data/prefixes.json'), true);

        foreach($prefixes as $prefix => $uri)
        {
            \Ontology::create(array(
                'prefix' => $prefix,
                'uri' => $uri,
            ));
        }

        $this->command->info("Added the prefixes and corresponding uri's.");
    }
}