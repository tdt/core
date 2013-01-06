<?php

/**
 * Installation step: system check
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */
class SystemCheck extends InstallController {

    public function index() {
        $tests = array();
        $extensions = get_loaded_extensions();

        // PHP version
        $tests["php_version"] = $this->checkVersion(PHP_VERSION, "5.3.1");

        // MySQL version
        if (in_array("mysql", $extensions) || in_array("mysqli", $extensions)) {
            $tests["mysql_version"] = $this->checkVersion(@mysql_get_server_info(), "5");
        }
        // SQLite
        elseif (in_array("SQLite", $extensions)) {
            $tests["sqlite_version"] = $this->checkVersion(@sqlite_libversion(), "3");
        }
        // PostgreSQL
        elseif (in_array("pgsql", $extensions)) {
            $tests["postgresql_version"] = $this->checkVersion("", "8");
        }

        // Memcache
        try {
           class_exists("Memcache");
           $tests["memcache_version"] = array("status" => "passed", "value" => "");
            
        } catch (Exception $ex) {
            $tests["memcache_version"] = array("status" => "failed", "value" => "");
        }


        $data["tests"] = $tests;
        $this->view("system", $data);
    }

    private function checkVersion($version = 0, $required = 0) {
        if (!$version) {
            return array("status" => "warning", "value" => "N/A", "message" => lang("system_version_not_tested"));
        } elseif (version_compare($version, $required) >= 0) {
            return array("status" => "passed", "value" => $version);
        } else {
            // don't allow next step on error
            $this->installer->nextStep(FALSE);
            return array("status" => "failed", "value" => $version, "message" => lang("system_version_low") . " " . $required);
        }
    }

}