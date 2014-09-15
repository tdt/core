<?php

namespace Tdt\Core\Repositories\Interfaces;

/**
 * An interface for the general settings of the datatank
 */

interface SettingsRepositoryInterface
{

    public function getValue($key);

    public function keyExists($key);

    public function storeValue($key, $value);

    public function deleteValue($key);

    public function getAll();
}
