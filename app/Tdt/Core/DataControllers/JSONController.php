<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Cache\Cache;
use Tdt\Core\Datasets\Data;
use ML\JsonLD\JsonLD;
use ML\JsonLD\NQuads;
use EasyRdf\Graph;

ini_set('default_socket_timeout', 5);

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
    private $bnodeMap = [];

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
        $graph = $this->parseJsonLD($uri);

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

        $config = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 2,
                )
            ));

        if (! filter_var($uri, FILTER_VALIDATE_URL) === false) {
            $parts = parse_url($uri);
            if ($parts['scheme'] != 'file') {
                $data = $this->getRemoteData($uri);
            } else {
                $data = @ file_get_contents($uri, $config);
            }
        } else {
            $data = @ file_get_contents($uri, $config);
        }

        if ($data) {
            Cache::put($uri, $data, $this->cache);
        } else {
            \App::abort(500, "Cannot retrieve data from the JSON file located on $uri.");
        }

        return $data;
    }

    private function parseJsonLD($uri)
    {
        $quads = JsonLD::toRdf($uri);
        $nquads = new NQuads();
        $graph = new Graph();

        foreach ($quads as $quad) {
            $subject = (string) $quad->getSubject();
            if ('_:' === substr($subject, 0, 2)) {
                $subject = $this->remapBnode($subject, $graph);
            }

            $predicate = (string) $quad->getProperty();

            if ($quad->getObject() instanceof \ML\IRI\IRI) {
                $object = array(
                    'type' => 'uri',
                    'value' => (string) $quad->getObject()
                );

                if ('_:' === substr($object['value'], 0, 2)) {
                    $object = array(
                        'type' => 'bnode',
                        'value' => $this->remapBnode($object['value'], $graph)
                    );
                }
            } else {
                $object = array(
                    'type' => 'literal',
                    'value' => $quad->getObject()->getValue()
                );

                if ($quad->getObject() instanceof \ML\JsonLD\LanguageTaggedString) {
                    $object['lang'] = $quad->getObject()->getLanguage();
                } else {
                    $object['datatype'] = $quad->getObject()->getType();
                }
            }
            $graph->add($subject, $predicate, $object);
        }

        return $graph;
    }

    private function getRemoteData($url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_MAXREDIRS, 10);
        $follow_allowed = ( ini_get('open_basedir') || ini_get('safe_mode')) ? false : true;

        if ($follow_allowed) {
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        }

        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
        curl_setopt($c, CURLOPT_REFERER, $url);
        curl_setopt($c, CURLOPT_TIMEOUT, 2);
        curl_setopt($c, CURLOPT_AUTOREFERER, true);
        curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
        $data = curl_exec($c);
        $status = curl_getinfo($c);
        curl_close($c);

        preg_match('/(http(|s)):\/\/(.*?)\/(.*\/|)/si', $status['url'], $link);
        $data = preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/|\/)).*?)(\'|\")/si', '$1=$2' . $link[0] . '$3$4$5', $data);

        $data = preg_replace('/(src|href|action)=(\'|\")((?!(http|https|javascript:|\/\/)).*?)(\'|\")/si', '$1=$2' . $link[1] . '://' . $link[3] . '$3$4$5', $data);

        if ($status['http_code'] == 200) {
            return $data;
        } elseif ($status['http_code'] == 301 || $status['http_code'] == 302) {
            \App::abort(400, 'The JSON URL redirected us to a different URI.');
        } elseif ($status > 300) {
            \App::abort(400, 'The JSON source is not available at this moment.');
        }

        return $data;
    }

    protected function remapBnode($name, $graph)
    {
        if (! isset($this->bnodeMap[$name])) {
            $this->bnodeMap[$name] = $graph->newBNodeId();
        }
        return $this->bnodeMap[$name];
    }
}
