<?php

/**
 * Seeder for dcat meta-data
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
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
    }

    /**
     * Seed the licenses
     *
     * @return void
     */
    private function seedLicenses()
    {
        // Empty the licenses table
        \License::truncate();

        // Fetch the licenses from the json file
        $licenses = json_decode(file_get_contents(app_path() . '/database/seeds/data/licenses.json'));

        foreach($licenses as $license)
        {
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

        $this->command->info('Added the licenses.');
    }

    /**
     * Seed the languages
     *
     * @return void
     */
    private function seedLanguages()
    {
        // Empty the languages table
        \Language::truncate();

        // Fetch the languages from the json file
        $languages = json_decode(file_get_contents(app_path() . '/database/seeds/data/languages.json'));

        foreach($languages as $language)
        {
            \Language::create(array(
                'lang_id' => $language->id,
                'lang_code' =>$language->lang_code,
                'name' => $language->name,
            ));
        }

        $this->command->info('Added the languages.');
    }
}