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
     * Return the value for the given key
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getValue($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!empty($setting)) {
            return $setting->value;
        }

        return null;
    }

    /**
     * Return a boolean indicating if the given key exists in the settings
     *
     * @param string $key
     *
     * @return boolean
     */
    public function keyExists($key)
    {
        return !is_null($this->getValue($key));
    }

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
     * Delete a value in the settings
     *
     * @param string $key
     * @param string $value
     *
     * @return boolean|null
     */
    public function deleteValue($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (!empty($setting)) {
            return $setting->delete();
        }

        return null;
    }

    /**
     * Get all the key value pairs that are in the settings table
     *
     * @return array
     */
    public function getAll()
    {
        $all_settings = array();

        $settings = Setting::all(array('key', 'value'))->toArray();

        foreach ($settings as $setting) {
            $all_settings[$setting['key']] = $setting['value'];
        }

        return $all_settings;
    }
}
