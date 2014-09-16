<?php

namespace Tdt\Core\Repositories\Interfaces;

/**
 * An interface for the general settings of the datatank
 */

interface SettingsRepositoryInterface
{

    public function storeValue($key, $value);

    public function getAll();
}
