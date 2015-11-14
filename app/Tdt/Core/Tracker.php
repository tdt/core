<?php

namespace Tdt\Core;

/**
 * Tracker class.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */

use TheIconic\Tracking\GoogleAnalytics\Analytics;

class Tracker
{
    private static $IGNORE = ['api', 'discovery'];

    public static function track($request, $tracker_id)
    {
        $path = $request->path();
        $extension = '';

        if (strpos($path, '.') !== false) {
            $uri_parts = explode('.', $request->path());

            $extension = strtolower(array_pop($uri_parts));
            $path = implode('', $uri_parts);
        } else {
            $extension = 'html';
        }

        // Get some meta-data from the definition to add to the GA as a dimension
        $definitions = \App::make('Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface');
        $definition = $definitions->getByIdentifier($path);

        $rights = 'Not provided';
        $theme = 'Not provided';

        if (!empty($definition['rights'])) {
            $rights = $definition['rights'];
        }

        if (!empty($definition['theme'])) {
            $theme = $definition['theme'];
        }

        // Get the target audience and category.
        $segments = $request->segments();

        if (count($segments) >= 2 && !in_array($segments[0], self::$IGNORE)) {
            $analytics = new Analytics(true);

            // Build the GA hit using the Analytics class methods
            // they should Autocomplete if you use a PHP IDE
            $analytics
                ->setProtocolVersion('1')
                ->setTrackingId($tracker_id)
                ->setClientId('555')
                ->setDocumentPath($path)
                ->setCustomDimension($extension, 1)
                ->setCustomDimension($rights, 2)
                ->setCustomDimension($theme, 3);

            // When you finish bulding the payload send a hit (such as an pageview or event)
            $analytics->sendPageview();
        }
    }
}
