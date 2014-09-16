<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\SettingsRepositoryInterface;
use Setting;

/**
 * A repository to handle the general settings of the datatank
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @license AGPLv3
 */

class SettingsRepository implements SettingsRepositoryInterface
{

    /**
     * Store/update a value in the settings
     *
     * @param string $key
     * @param string $value
     *
     * @return int
     */
    public function storeValue($key, $value)
    {
        // Overwrite the value if the key is already in the table
        $setting = new Setting();
        $setting->key = $key;
        $setting->value = $value;

        return $setting->save();
    }

    /**
     * Get all the key value pairs that are in the settings table
     *
     * @return array
     */
    public function getAll()
    {
        $all_settings = array(
                            'catalog_title' => 'The DataTank',
                            'catalog_description' => 'A catalog of datasets published by The DataTank.',
                            'catalog_language' => 'en',
                            'catalog_publisher_uri' => 'http://thedatatank.com',
                            'catalog_publisher_name' => 'The DataTank',
                        );

        $settings = Setting::all(array('key', 'value'))->toArray();

        foreach ($settings as $setting) {
            $all_settings[$setting['key']] = $setting['value'];
        }

        return $all_settings;
    }
}
