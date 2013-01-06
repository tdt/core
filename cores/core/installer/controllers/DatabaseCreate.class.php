<?php

/**
 * Installation step: database create
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */
class DatabaseCreate extends InstallController {

    public function index() {
        if (isset($_POST["user"]) && isset($_POST["pass"])) {
            $this->createDatabase($_POST["user"], $_POST["pass"]);
        } else {
            // try installation with config credentials
            $this->createDatabase(Config::get("db", "user"), Config::get("db", "password"));
        }
    }

    private function createDatabase($user, $pass) {

        try {
            $db_string = Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name");
            $db_config = explode(";", $db_string);
            $dbname = end($db_config);
            $pieces = explode("=", $dbname);

            // get database name
            if (isset($pieces) && $pieces[0] == "dbname") {
                $dbname = $pieces[1];
                $db = str_replace(";dbname=" . $dbname, "", $db_string);

                R::setup($db, $user, $pass);

                $query = "CREATE DATABASE IF NOT EXISTS " . $dbname . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
                R::exec($query);

                $data["status"] = "passed";
            } else {
                $data["status"] = "failed";
                $data["message"] = "database_no_database";

                $this->installer->nextStep(FALSE);
                $this->installer->previousStep("DatabaseCheck");
            }

            // show database create success page
            $this->view("database_create", $data);
        } catch (Exception $e) {
            $data["status"] = "failed";
            $data["message"] = $e->getMessage();

            $this->installer->nextStep(FALSE);
            $this->installer->previousStep("DatabaseCheck");

            $this->view("database_root", $data);
        }
    }

}