<?php

namespace Tdt\Core\Validators;

/**
 * A custom validator that provides extra functions
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class CustomValidator extends \Illuminate\Validation\Validator
{
    /**
     * Check if the URI can be resolved externally or locally
     */
    public function validateUri($attribute, $value, $parameters)
    {
        try {
            $url_pieces = parse_url($value);

            if (!filter_var($value, FILTER_VALIDATE_URL) === false && ($url_pieces['scheme'] == 'http' || $url_pieces['scheme'] == 'https')) {
                $status = $this->getHeadInfo($value);

                return $status == 200;
            } else {
                $data =@ file_get_contents($value);

                return !empty($data);
            }
        } catch (\Exception $ex) {
            return false;
        }
    }

    private function getHeadInfo($uri)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $uri);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
        curl_setopt($c, CURLOPT_TIMEOUT, 60);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_setopt($c, CURLOPT_NOBODY, true);

        curl_exec($c);
        $status = curl_getinfo($c);
        curl_close($c);

        if (!empty($status['http_code'])) {
            return $status['http_code'];
        } else {
            return 500;
        }
    }

    private function getRemoteData($url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_MAXREDIRS, 10);
        $follow_allowed= ( ini_get('open_basedir') || ini_get('safe_mode')) ? false:true;

        if ($follow_allowed) {
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        }

        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 9);
        curl_setopt($c, CURLOPT_REFERER, $url);
        curl_setopt($c, CURLOPT_TIMEOUT, 60);
        curl_setopt($c, CURLOPT_AUTOREFERER, true);
        curl_setopt($c, CURLOPT_ENCODING, 'gzip,deflate');
        $data = curl_exec($c);
        $status = curl_getinfo($c);
        curl_close($c);

        return $data;
    }

    /**
     * Check if the given value is a proper file that can be opened with fopen().
     */
    public function validateFile($attribute, $value, $parameters)
    {

        try {
            $handle = fopen($value, 'r');
            return $handle;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Check if the given file contains a JSON body.
     */
    public function validateJson($attribute, $value, $parameters)
    {
        try {
            $data = [];

            if (!filter_var($value, FILTER_VALIDATE_URL) === false) {
                $ch = curl_init();
                $data = $this->getRemoteData($value);
                curl_close($ch);
            } else {
                \Log::info("fetching contents");
                $data =@ file_get_contents($value);
            }

            if (empty($data)) {
                return false;
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Check if the given file is located in the installed folder and the
     */
    public function validateInstalled($attribute, $value, $parameters)
    {
        try {
            $class_file = app_path() . '/../installed/' .  $value;

            return file_exists($class_file);

        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Check if the SPARQL query is legitimate
     */
    public function validateSparqlquery($attribute, $value, $parameters)
    {
        if (stripos($value, 'construct') === false && stripos($value, 'select') === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if the MySQL query is legitimate
     */
    public function validateMysqlquery($attribute, $value, $parameters)
    {
        if (stripos($value, 'select') === false || stripos($value, 'from') === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if the collection uri doesn't contain preserved namespaces
     */
    public function validateCollectionuri($attribute, $value, $parameters)
    {
        $preserved_ns = array('discovery', 'api');

        $collection_uri = explode('/', $value);

        if (in_array(strtolower($collection_uri[0]), $preserved_ns)) {
            return false;
        }

        return true;
    }
}
