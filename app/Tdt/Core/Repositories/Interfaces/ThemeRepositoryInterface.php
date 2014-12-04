<?php

namespace Tdt\Core\Repositories\Interfaces;

interface ThemeRepositoryInterface
{

    /**
     * Fetch a theme by uri
     *
     * @param string $uri
     * @return array Theme
     */
    public function getByUri($uri);

    /**
     * Fetch all of the themes
     *
     * @return array of theme
     */
    public function getAll();

    /**
     * Fetch a theme by
     *
     * @param string $label
     * @return array Theme
     */
    public function getByLabel($label);
}
