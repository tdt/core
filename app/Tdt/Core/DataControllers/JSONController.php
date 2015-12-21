<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Cache\Cache;
use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * JSON Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class JSONController extends ADataController
{
    public static function getParameters()
    {
        return [];
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        $uri = $source_definition['uri'];

        $this->cache = $source_definition['cache'];

        switch ($source_definition['jsontype']) {
            case 'GeoJSON':
                return $this->makeGeoResponse($uri);
                break;
            case 'JSON-LD':
                return $this->makeSemanticResponse($uri);
                break;
            default:
                return $this->makePlainResponse($uri);
                break;
        }
    }

    private function makeGeoResponse($uri)
    {
        $data = $this->getPlainJson($uri);

        $php_object = json_decode($data);

        $data_result = new Data();
        $data_result->data = $php_object;
        $data_result->preferred_formats = ['geojson'];
        $data_result->geo_formatted = true;

        return $data_result;
    }

    private function makeSemanticResponse($uri)
    {
        try {
            $graph = new \EasyRdf_Graph();

            if ((substr($uri, 0, 4) == "http")) {
                $graph = \EasyRdf_Graph::newAndLoad($uri);
            } else {
                $graph->parseFile($uri, 'jsonld');
            }

        } catch (\Exception $ex) {
            \App::abort(500, "The JSON-LD reader couldn't parse the document, the exception message we got is: " . $ex->getMessage());
        }

        // Return the data object with the graph
        $data = new Data();
        $data->data = $graph;
        $data->is_semantic = true;
        $data->preferred_formats = ['jsonld', 'ttl', 'rdf'];

        return $data;
    }

    private function makePlainResponse($uri)
    {
        $data = $this->getPlainJson($uri);

        $php_object = json_decode($data);

        $data_result = new Data();
        $data_result->data = $php_object;
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }

    private function getPlainJson($uri)
    {
        $data = [];

        if (Cache::has($uri)) {
            return Cache::get($uri);
        }

        if (!filter_var($uri, FILTER_VALIDATE_URL) === false) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            $data =@ file_get_contents($uri);
        }

        if ($data) {
            Cache::put($uri, $data, $this->cache);
        } else {
            \App::abort(500, "Cannot retrieve data from the JSON file located on $uri.");
        }

        return $data;
    }
}
