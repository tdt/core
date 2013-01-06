<?php

/**
 * Installation step: database check
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */
class DatabaseCheck extends InstallController {

    public function index() {

        $data["credentials"]["DB"] = Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name");
        $data["credentials"]["DB_USER"] = Config::get("db", "user");
        $data["credentials"]["DB_PASSWORD"] = ""; // don't output real password

        for ($i = 0; $i < strlen(Config::get("db", "password")); $i++)
            $data["credentials"]["DB_PASSWORD"] .= "*";

        error_reporting(E_ALL | E_STRICT);

        // detect database name
        $db_pieces = explode(";", Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"));
        $dbname = end($db_pieces);
        $pieces = explode("=", $dbname);
        if (!isset($pieces) || $pieces[0] != "dbname") {
            $data["status"] = "failed";
            $data["message"] = "database_no_database";
        } else {
            try {
                // try a simple query to test redbean's connection
                R::setup(Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"), Config::get("db", "user"), Config::get("db", "password"));
                R::exec("SELECT 'hello'");

                $data["status"] = "passed";

                // we can connect, so database should exist
                $this->installer->nextStep("DatabaseSetup");
            } catch (Exception $e) {
                // if database does not exist we will create it in next step
                if (stristr($e->getMessage(), "Unknown database")) {
                    $this->installer->nextStep("DatabaseCreate");
                    $data["status"] = "warning";
                    $data["message"] = "database_create_next_step";
                } else {
                    $data["status"] = "failed";
                    $data["message"] = $e->getMessage();
                }
            }
        }

        // don't allow next step on error
        if ($data["status"] == "failed")
            $this->installer->nextStep(FALSE);

        $this->view("database_check", $data);
    }

}