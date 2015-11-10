<?php

class GeoSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Eloquent::unguard();

        // Seed the database with ontology prefixes
        $this->seedGeoprojections();
    }

    /**
     * Seed the prefixes table based on the prefixes json file
     *
     * @return void
     */
    private function seedGeoprojections()
    {
        // Empty the ontology table
        \Geoprojection::truncate();

        // Fetch the ontology from the json file
        $geoprojections = json_decode(file_get_contents(app_path() . '/database/seeds/data/geoprojections.json'));

        if (!empty($geoprojections)) {
            \Geoprojection::truncate();

            foreach ($geoprojections as $language) {
                \Geoprojection::create(array(
                    'epsg' => $language->epsg,
                    'projection' =>$language->projection,
                ));
            }

            $this->command->info("Added the geographical projections.");
        }
    }
}
