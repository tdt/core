<?php

/**
 * Seeder for dcat meta-data
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DcatSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Seed the licenses
        $this->seedLicenses();

        // Seed the languages
        $this->seedLanguages();

        // Seed the themes
        $this->seedThemes();
    }

    /**
     * Seed the licenses
     *
     * @return void
     */
    private function seedLicenses()
    {
        // Fetch the licenses from the json file
        $this->command->info('---- DCAT Licenses ----');
        $this->command->info('Trying to fetch the licenses from a local json file.');

        $licenses = json_decode(file_get_contents(app_path() . '/database/seeds/data/licenses.json'));

        if (!empty($licenses)) {

            $this->command->info('Licenses have been found, deleting the current ones, and replacing them with the new ones.');

            // Empty the licenses table
            $this->command->info('Emptying the current licenses table.');

            \License::truncate();

            foreach ($licenses as $license) {
                \License::create(array(
                    'domain_content' => $license->domain_content,
                    'domain_data' => $license->domain_data,
                    'domain_software' => $license->domain_software,
                    'family' => $license->family,
                    'license_id' => $license->license_id,
                    'is_generic' => @$license->is_generic,
                    'is_okd_compliant' => $license->is_okd_compliant,
                    'is_osi_compliant' => $license->is_osi_compliant,
                    'maintainer' => $license->maintainer,
                    'status' => $license->status,
                    'title' => $license->title,
                    'url' => $license->url
                    ));
            }

            $this->command->info('Added the licenses from a local json file.');

        } else {
            $this->command->info('The licenses from the json file were empty, the old ones will not be replaced.');
        }
    }

    /**
     * Seed the languages
     *
     * @return void
     */
    private function seedLanguages()
    {
        $this->command->info('---- DCAT Languages ----');

        // Fetch the languages from the json file
        $this->command->info('Trying to fetch the languages from the local json file.');

        // Fetch the languages from the json file
        $languages = json_decode(file_get_contents(app_path() . '/database/seeds/data/languages.json'));

        if (!empty($languages)) {

            $this->command->info('Languages have been found, deleting the current ones, and replacing them with the new ones.');

            $this->command->info('Emptying the current languages table.');

            // Empty the languages table
            \Language::truncate();

            foreach ($languages as $language) {
                \Language::create(array(
                    'lang_id' => $language->lang_id,
                    'lang_code' =>$language->lang_code,
                    'name' => $language->name,
                    ));
            }

            $this->command->info('Added the languages from a local json file.');

        } else {
            $this->command->info('No languages have not been found, the old ones will not be replaced.');
        }
    }

    /**
     * Seed the themes
     *
     * return @void
     */
    private function seedThemes()
    {
        $this->command->info('---- DCAT Themes ----');

        $base_uri = 'http://ns.thedatatank.com/dcat/themes';

        $uri = $base_uri . '.ttl';

        $themes_fetched = false;

        // Try to get the themes from the ns.thedatatank.com (semantic data)
        try {

            $this->command->info('Trying to fetch new themes online.');

            $themes_graph = \EasyRdf_Graph::newAndLoad($uri);

            if ($themes_graph->isEmpty()) {
                $this->command->info('We could not reach the online themes.');
            } else {
                $themes_fetched = true;

                $this->command->info('Found new themes online, removing the old ones.');

                // Empty the themes table
                \Theme::truncate();
            }

            // Fetch all of the themes
            foreach ($themes_graph->resources('skos:inScheme', $uri . '#Taxonomy') as $theme) {

                $uri = $theme->getUri();

                $label = $theme->getLiteral('rdfs:label');

                if (!empty($label) && !empty($uri)) {

                    $label = $label->getValue();

                    \Theme::create(array(
                        'uri' => $uri,
                        'label' => $label
                        ));
                }
            }

            $this->command->info('Added new themes.');

        } catch (EasyRdf_Exception $ex) {
            $this->command->info('An error occurred when we tried to fetch online themes.');
        }

        // If it's not available, get them from a file (json)
        if (!$themes_fetched) {

            $this->command->info('Trying to fetch the themes from the local json file containing a default set of themes.');

            $themes = json_decode(file_get_contents(app_path() . '/database/seeds/data/themes.json'));

            if (!empty($themes)) {

                $this->command->info('Found new themes, removing the old ones.');

                // Empty the themes table
                \Theme::truncate();

                foreach ($themes as $theme) {
                    \Theme::create(array(
                        'uri' => $theme->uri,
                        'label' => $theme->label,
                        ));
                }

                if (!empty($themes)) {
                    $this->command->info('Added themes from the local json file.');
                }

            } else {
                $this->command->info('No themes were found in the local json file, the old ones will not be replaced.');
            }
        }
    }
}
