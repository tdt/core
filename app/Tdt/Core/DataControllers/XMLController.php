<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Cache\Cache;
use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;
use Tdt\Core\utils\XMLSerializer;

/**
 * XML Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class XMLController extends ADataController
{
    public static function getParameters()
    {
        return [];
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        $uri = $source_definition['uri'];

        // Keep track of the prefix URI's
        $this->prefixes = array();

        // Check for caching
        if (Cache::has($uri)) {
            $data = Cache::get($uri);
        } else {
            // Fetch the data
            $data =@ file_get_contents($uri);

            if (!empty($data)) {
                Cache::put($uri, $data, $source_definition['cache']);
            } else {
                $uri = $source_definition['uri'];
                \App::abort(500, "Cannot retrieve data from the XML file located on $uri.");
            }
        }

        $data_result = new Data();
        $data_result->data = $data;
        $data_result->semantic = $this->prefixes;
        $data_result->preferred_formats = $this->getPreferredFormats();

        if (!empty($source_definition['geo_formatted']) && $source_definition['geo_formatted']) {
            $data_result->geo_formatted = true;
            $data_result->preferred_formats = array('geojson', 'map');
        }

        return $data_result;
    }

    /**
     * Provide an array a formatter priorities
     */
    protected function getPreferredFormats()
    {
        // Both semantic and raw data structures support json
        return array('xml');
    }
}
