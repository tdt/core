<?php

namespace Tdt\Core;

/**
 * Tracker class.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */

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

        $license = 'Not provided';
        $theme = 'Not provided';

        if (!empty($definition['rights'])) {
            $license = $definition['rights'];
        }

        if (!empty($definition['theme'])) {
            $theme = $definition['theme'];
        }

        // Get the target audience and category.
        $segments = $request->segments();

        if (count($segments) >= 2 && !in_array($segments[0], self::$IGNORE)) {
            // The URL of the GA
            $url = 'http://www.google-analytics.com/collect';

            // The version of the Google Analytics Measurement Protocol.
            $data['v'] = 1;

            // The tracker ID.
            $data['tid'] = $tracker_id;

            // GA requires a user ID, but since all requests are anonymous
            // we generate a unique string to use as ID.
            $data['cid'] = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );

            // The type of 'hit', in this case we use pageview.
            $data['t'] = 'pageview';

            // The url to track, required when using 'pageview' as type.
            $data['dp'] = $path;
            $data['cd1'] = $extension;
            $data['cd2'] = $theme;
            $data['cd3'] = $license;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, utf8_encode(http_build_query($data)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
        }
    }
}
