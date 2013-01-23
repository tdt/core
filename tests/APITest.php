<?php

/**
 * An class that provides end2end API testing
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */
require "vendor/autoload.php";

use tdt\core\utility\Config;

class APITest extends \PHPUnit_Framework_TestCase {

    private $config = array();

    public function __construct($config) {
        /* $a = array(["general"] => array(
          ["hostname"] => string(17) "http://localhost/" ["subdir"] => string(13) "start/public/" ["timezone"] => string(15) "Europe/Brussels" ["defaultlanguage"] => string(2) "en" ["defaultformat"] => string(4) "json" ["cache"] => array(3) {
          ["system"] => string(7) "NoCache" ["host"] => string(9) "localhost" ["port"] => int(11211)
          ) ["faultinjection"] => array(
          ["enabled"] => bool(true) ["period"] => int(1000)
          ) ["auth"] => array(
          ["enabled"] => bool(true) ["api_user"] => string(3) "tdt" ["api_passwd"] => string(3) "tdt"
          } ["logging"] => array(2) {
          ["enabled"] => bool(true) ["path"] => string(16) "/var/log/tdtLogs"
          }
          } ["routes"] => array(7) {
          ["GET | /error/(4..|5..|critical)/?.*"] => string(15) "ErrorController" ["GET | /documentation/?"] => string(23) "DocumentationController" ["GET | /?"] => string(23) "DocumentationController" ["GET | (?P.*)\.(?P[^?]+).*\??(.*)"] => string(32) "tdt\core\controllers\RController" ["GET | TDTAdmin/Resources/(?P.*)"] => string(34) "tdt\core\controllers\CUDController" ["PUT | TDTAdmin/Resources/(?P.*)"] => string(34) "tdt\core\controllers\CUDController" ["GET | (?P.*)"] => string(39) "tdt\core\controllers\RedirectController"
          } ["db"] => array(5) {
          ["system"] => string(5) "mysql" ["host"] => string(9) "localhost" ["name"] => string(3) "tdt" ["user"] => string(9) "superuser" ["password"] => string(9) "superuser"
          }); */
        $config = array("general" => array("hostname" => "", "subdir" => "", "defaultformat" => "json"),
            "cache" => array("system" => "NoCache","host"=>"", "port"=>""),
            "db" => array("system" => "mysql", "host"=>"localhost","user"=>"root", "password" => ""));
    }

    /*
     * Test function to check if a CSV file is added and removed correctly
     * and in between can be read as well in a correct way. Needs curl to work!
     */

    public function testCSV() {
        $hostname = Config::get("general", "hostname");
        $subdir = Config::get("general", "subdir");

        $url = $hostname . $subdir . "TDTAdmin/Resources/testcasecsv/csv1";
        $username = Config::get("general", "auth", "username");
        $password = Config::get("general", "auth", "password");

        $fields = array(
            'documentation' => urlencode("this is a test case for unittesting a CSV resource"),
            'resource_type' => urlencode("generic/CSV"),
            'delimiter' => urlencode(";"),
            'uri' => urlencode(__DIR__ . "/data/CSVData.csv")
        );



        //open connection
        $ch = curl_init();

        // configure the HTTP Request 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, "' . $username . ':' . $password . '");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        $result = curl_exec($ch);
        $responseHeader = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        var_dump($responseHeader);
        //close connection
        curl_close($ch);
    }

}