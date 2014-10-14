<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\ThemeRepositoryInterface;

class ThemeRepository implements ThemeRepositoryInterface
{

    /**
     * Fetch a Theme by its uri (=id)
     */
    public function getByUri($uri)
    {
        $theme = \Theme::where('uri', '=', $uri)->first();

        if (!empty($theme)) {
            return $theme->toArray();
        }

        return $theme;
    }

    public function getAll()
    {
        return \Theme::all(
            array(
                'uri',
                'label'
            )
        )->toArray();
    }

    public function getByLabel($label)
    {
        $theme = \Theme::where('label', '=', $label)->first();

        if (!empty($theme)) {
            return $theme->toArray();
        }

        return $theme;
    }
}
