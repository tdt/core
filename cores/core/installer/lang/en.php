<?php

$lang["next_button"] = "next";
$lang["previous_button"] = "previous";

$lang["welcome_title"] = "The DataTank ".Installer::version()." installer";
$lang["welcome_message"] = "This process will take you through the needed installation steps for your DataTank ".Installer::version()." setup.";

$lang["config_check_title"] = "Config verification";
$lang["config_check_message"] = "A configuration file has been found and its values will be checked, please confirm or adjust these values:";
$lang["no_config"] = "Your configuration file was not found. Please rename Config.example.class.php to Config.class.php and adjust the settings to your specific environment.";
$lang["hostname_no_match"] = "Your hostname does not match the current server name: ".$_SERVER["SERVER_NAME"];
$lang["hostname_no_https"] = "We encourage the use of https";
$lang["cache_not_supported"] = "This cache system is not supported";
$lang["memcache_not_installed"] = "Memcache is not installed, please use NoCache";
$lang["cache_no_memcache"] = "We encourage the use of MemCache";
$lang["cache_wrong_credentials"] = "Please check your cache settings";
$lang["cache_not_tested"] = "Could not test the caching system, check error message above";
$lang["subdir_detected"] = "We detected a subdirectory";
$lang["subdir_wrong"] = "We detected a different subdirectory (don't forget your trailing slash)";
$lang["api_no_user"] = "No API username given";
$lang["api_no_pass"] = "No API password given";
$lang["api_short_pass"] = "API password should be at least 6 characters";

$lang["system_title"] = "System requirements";
$lang["system_message"] = "Your system configuration will now be matched with our minimum requirements.";
$lang["php_version"] = "PHP version";
$lang["mysql_version"] = "MySQL version";
$lang["sqlite_version"] = "SQLite version";
$lang["postgresql_version"] = "PostgreSQL version";
$lang["memcache_version"] = "Memcache";

$lang["system_version_not_tested"] = "Could not test your version";
$lang["system_version_low"] = "Your version should be at least";

$lang["database_title"] = "Database check";
$lang["database_message"] = "Your database config credentials will now be verified so that we can create the needed database.";
$lang["database_credentials_wrong"] = "Please verify your database settings";
$lang["database_credentials_ok"] = "Your database settings have been verified";
$lang["database_create_next_step"] = "The database you selected does not exist, we will create it in the next step";
$lang["database_no_database"] = "You did not specify a database";

$lang["database_create_title"] = "Database creation";
$lang["database_create_message"] = "Your database will now be created";
$lang["database_create_success"] = "Your database has been created";
$lang["database_create_failed"] = "Your database could not be created, please return to the previous step and verify your credentials";

$lang["database_root_title"] = "Database root login";
$lang["database_root_message"] = "We tried creating the database with the supplied credentials, but this account does not have the authorization to create databases. Please provide credentials that do allow the creation of database, this information will not be saved to your config.";

$lang["database_setup_title"] = "Database setup";
$lang["database_setup_message"] = "Your database will now be prepared for your DataTank";
$lang["database_table_created"] = "Created";
$lang["database_table_failed"] = "Could not create table";
$lang["database_setup_success"] = "Your database tables have been created";
$lang["database_setup_failed"] = "One or more tables could not be created, please check your database settings and try again";

$lang["finish_title"] = "DataTank ".Installer::version()." installation completed";
$lang["finish_message"] = "Your DataTank has been installed. Further information can be found on:<br>
 our <a href='http://github.com/iRail/The-DataTank'> github page</a> <br>
 our <a href='http://thedatatank.org'>wiki page </a> <br><br><br>
 To get you started with some interesting default resources check out:<br>
 <a href='" . Config::get("general","hostname") . Config::get("general","subdir") . "TDTInfo'> public package and resource information </a><br>
 <a href='" . Config::get("general","hostname") . Config::get("general","subdir") . "TDTAdmin'> private package and resource information </a><br>
 <br> Interested on knowing what has been changed since the last release? Check out the release notes which be found in the main folder of your datatank.<br>";
