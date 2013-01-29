<?php

/**
 * Helper classes that are specifically designed for TDT. When developing modules you can use these for better performance
 * 
 * @package framework
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\core\utility;

use tdt\cache\Cache;
use tdt\exceptions\TDTException;

class Request {

    private static $HTTP_REQUEST_TIMEOUT = 10; // set the standard timeout to 10
    private static $CACHE_TIME = 60; // set the default caching time to 60 seconds

    /**
     * The HttpRequest stolen from Drupal 7. Drupal is licensed GPLv2 or later. This is compatible with our AGPL license.
     * Use this function to get some content
     * @param string $url The url for the request
     * @param array $options Additional arguments to pass along the httprequest.
     * @return mixed Returns errorcode or result of the httprequest.
     */

    public static function http($url, array $options = array()) {
        // Parse the URL and make sure we can handle the schema.
        $uri = @parse_url($url);

        if ($uri == FALSE) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array($url), $exception_config);
        }
        //maybe our result is the cache. If so, return the cache value
        $cache_config = array();

        $cache_config["system"] = Config::get("general", "cache", "system");
        $cache_config["host"] = Config::get("general", "cache", "host");
        $cache_config["port"] = Config::get("general", "cache", "port");

        $cache = Cache::getInstance($cache_config);
        //Generate a cachekey for the url and the post data
        $cachekey = "";
        if (isset($options["data"])) {
            $cachekey = md5(urlencode($url) . md5($options["data"]));
        } else {
            $cachekey = md5(urlencode($url));
        }
        //DEBUG echo $url . " " . $cachekey . "<br/>\n";
        $result = $cache->get($cachekey);
        if (!is_null($result)) {
            return $result;
        }

        //in any other case, just continue and add it to the cache afterwards
        $result = new \StdClass();

        if (!isset($uri['scheme'])) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Forgot to add http(s)? " . $url), $exception_config);
        }

        self::timer_start(__FUNCTION__);

        // Merge the default options.
        $options += array(
            'headers' => array(),
            'method' => 'GET',
            'data' => NULL,
            'max_redirects' => 3,
            'timeout' => 30.0,
            'context' => NULL,
        );
        // stream_socket_client() requires timeout to be a float.
        $options['timeout'] = (float) $options['timeout'];

        switch ($uri['scheme']) {
            case 'http':
            case 'feed':
                $port = isset($uri['port']) ? $uri['port'] : 80;
                $socket = 'tcp://' . $uri['host'] . ':' . $port;
                // RFC 2616: "non-standard ports MUST, default ports MAY be included".
                // We don't add the standard port to prevent from breaking rewrite rules
                // checking the host that do not take into account the port number.
                $options['headers']['Host'] = $uri['host'] . ($port != 80 ? ':' . $port : '');
                break;
            case 'https':
                // Note: Only works when PHP is compiled with OpenSSL support.
                $port = isset($uri['port']) ? $uri['port'] : 443;
                $socket = 'ssl://' . $uri['host'] . ':' . $port;
                $options['headers']['Host'] = $uri['host'] . ($port != 443 ? ':' . $port : '');
                break;
            default:
                $result->error = 'invalid schema ' . $uri['scheme'];
                $result->code = -1003;
                return $result;
        }

        if (empty($options['context'])) {
            $fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout']);
        } else {
            // Create a stream with context. Allows verification of a SSL certificate.
            $fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout'], STREAM_CLIENT_CONNECT, $options['context']);
        }

        // Make sure the socket opened properly.
        if (!$fp) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array($url), $exception_config);
        }

        // Construct the path to act on.
        $path = isset($uri['path']) ? $uri['path'] : '/';
        if (isset($uri['query'])) {
            $path .= '?' . $uri['query'];
        }

        // Merge the default headers.
        $options['headers'] += array(
            'User-Agent' => 'The DataTank 1.0', //TODO VERSION
        );

        // Only add Content-Length if we actually have any content or if it is a POST
        // or PUT request. Some non-standard servers get confused by Content-Length in
        // at least HEAD/GET requests, and Squid always requires Content-Length in
        // POST/PUT requests.
        $content_length = strlen($options['data']);
        if ($content_length > 0 || $options['method'] == 'POST' || $options['method'] == 'PUT') {
            $options['headers']['Content-Length'] = $content_length;
        }

        // If the server URL has a user then attempt to use basic authentication.
        if (isset($uri['user'])) {
            $options['headers']['Authorization'] = 'Basic ' . base64_encode($uri['user'] . (!empty($uri['pass']) ? ":" . $uri['pass'] : ''));
        }

        // If the database prefix is being used by SimpleTest to run the tests in a copied
        // database then set the user-agent header to the database prefix so that any
        // calls to other Drupal pages will run the SimpleTest prefixed database. The
        // user-agent is used to ensure that multiple testing sessions running at the
        // same time won't interfere with each other as they would if the database
        // prefix were stored statically in a file or database variable.
        $test_info = &$GLOBALS['drupal_test_info'];
        if (!empty($test_info['test_run_id'])) {
            $options['headers']['User-Agent'] = drupal_generate_test_ua($test_info['test_run_id']);
        }

        $request = $options['method'] . ' ' . $path . " HTTP/1.0\r\n";
        foreach ($options['headers'] as $name => $value) {
            $request .= $name . ': ' . trim($value) . "\r\n";
        }
        $request .= "\r\n" . $options['data'];
        $result->request = $request;
        // Calculate how much time is left of the original timeout value.
        $timeout = $options['timeout'] - self::timer_read(__FUNCTION__) / 1000;
        if ($timeout > 0) {
            stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
            fwrite($fp, $request);
        }

        // Fetch response. Due to PHP bugs like http://bugs.php.net/bug.php?id=43782
        // and http://bugs.php.net/bug.php?id=46049 we can't rely on feof(), but
        // instead must invoke stream_get_meta_data() each iteration.
        $info = stream_get_meta_data($fp);
        $alive = !$info['eof'] && !$info['timed_out'];
        $response = '';

        while ($alive) {
            // Calculate how much time is left of the original timeout value.
            $timeout = $options['timeout'] - self::timer_read(__FUNCTION__) / 1000;
            if ($timeout <= 0) {
                $info['timed_out'] = TRUE;
                break;
            }
            stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
            $chunk = fread($fp, 1024);
            $response .= $chunk;
            $info = stream_get_meta_data($fp);
            $alive = !$info['eof'] && !$info['timed_out'] && $chunk;
        }
        fclose($fp);

        if ($info['timed_out']) {
            $result->code = self::$HTTP_REQUEST_TIMEOUT;
            $result->error = 'request timed out';
            return $result;
        }
        // Parse response headers from the response body.
        // Be tolerant of malformed HTTP responses that separate header and body with
        // \n\n or \r\r instead of \r\n\r\n.
        list($response, $result->data) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
        $response = preg_split("/\r\n|\n|\r/", $response);

        // Parse the response status line.
        list($protocol, $code, $status_message) = explode(' ', trim(array_shift($response)), 3);
        $result->protocol = $protocol;
        $result->status_message = $status_message;

        $result->headers = array();

        // Parse the response headers.
        while ($line = trim(array_shift($response))) {
            list($name, $value) = explode(':', $line, 2);
            $name = strtolower($name);
            if (isset($result->headers[$name]) && $name == 'set-cookie') {
                // RFC 2109: the Set-Cookie response header comprises the token Set-
                // Cookie:, followed by a comma-separated list of one or more cookies.
                $result->headers[$name] .= ',' . trim($value);
            } else {
                $result->headers[$name] = trim($value);
            }
        }

        $responses = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported',
        );
        // RFC 2616 states that all unknown HTTP codes must be treated the same as the
        // base code in their class.
        if (!isset($responses[$code])) {
            $code = floor($code / 100) * 100;
        }
        $result->code = $code;

        switch ($code) {
            case 200: // OK
            case 304: // Not modified
                break;
            case 301: // Moved permanently
            case 302: // Moved temporarily
            case 307: // Moved temporarily
                $location = $result->headers['location'];
                $options['timeout'] -= self::timer_read(__FUNCTION__) / 1000;
                if ($options['timeout'] <= 0) {
                    $result->code = self::$HTTP_REQUEST_TIMEOUT;
                    $result->error = 'request timed out';
                } elseif ($options['max_redirects']) {
                    // Redirect to the new location.
                    $options['max_redirects']--;
                    $result = self::HttpRequest($location, $options);
                    $result->redirect_code = $code;
                }
                if (!isset($result->redirect_url)) {
                    $result->redirect_url = $location;
                }
                break;
            default:
                $result->error = $url;
        }

        //store the result in cache
        $cachingtime = self::$CACHE_TIME;
        if (isset($options["cache-time"])) { //are we sure we're going to call the option cache-time?
            $cachingtime = $options["cache-time"];
        }

        $cache->set($cachekey, $result, $cachingtime);

        return $result;
    }

    private static function timer_start($name) {
        global $timers;
        $timers[$name]['start'] = microtime(TRUE);
        $timers[$name]['count'] = isset($timers[$name]['count']) ? ++$timers[$name]['count'] : 1;
    }

    /**
     * Function needed by drupal for http request. 
     */
    private static function timer_stop($name) {
        global $timers;

        if (isset($timers[$name]['start'])) {
            $stop = microtime(TRUE);
            $diff = round(($stop - $timers[$name]['start']) * 1000, 2);
            if (isset($timers[$name]['time'])) {
                $timers[$name]['time'] += $diff;
            } else {
                $timers[$name]['time'] = $diff;
            }
            unset($timers[$name]['start']);
        }

        return $timers[$name];
    }

    /**
     * Function needed by drupal for http request ({@link HttpRequest()}).
     */
    private static function timer_read($name) {
        global $timers;

        if (isset($timers[$name]['start'])) {
            $stop = microtime(TRUE);
            $diff = round(($stop - $timers[$name]['start']) * 1000, 2);

            if (isset($timers[$name]['time'])) {
                $diff += $timers[$name]['time'];
            }
            return $diff;
        }
        return $timers[$name]['time'];
    }

}