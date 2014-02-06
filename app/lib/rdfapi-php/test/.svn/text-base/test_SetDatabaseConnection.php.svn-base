<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_SetDatabaseConnection.php
// ----------------------------------------------------------------------------------

/*
 * This is an online demo of RAP's database backend.
 * Tutorial how to connect to different databases and how to create tables.
 *
 * @version $Id$
 * @author Radoslaw Oldakowski <radol@gmx.de>
 */
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Test Store Models in Database</title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");


## 1. Connect to MsAccess database (via ODBC) and create tables.

// Connect to MsAccess (rdf_db DSN) database using connection settings
// defined in constants.php :
// ----------------------------------------------------------------------------------
// Database
// ----------------------------------------------------------------------------------
// define("ADODB_DB_DRIVER", "odbc");
// define("ADODB_DB_HOST", "rdf_db");
// define("ADODB_DB_NAME", "");
// define("ADODB_DB_USER", "");
// define("ADODB_DB_PASSWORD", "");
$rdf_database = new DbStore();


// Create tables for MsAccess
$rdf_database->createTables('MsAccess');



## 2. Connect to MySQL database and create tables.

/*
// Connect to MySQL database with user defined connection settings
$rdf_database = new DbStore('MySQL', 'localhost', 'db_name', 'user_name', 'password' );

// Create tables for MySQL
$rdf_database->createTables('MySQL');
*/



## 3. Connect to other databases

/*
// Example:
// Connect to Oracle database with user defined connection settings
$rdf_database = new DbStore('Oracle', FALSE, 'db_name', 'username', 'password' );

// Method createTables() currently supports only MySQL and MsAccess.
// If you want to use other databases, you will have to create tables by yourself
// according to the abstract database schema described in the API documentation.
*/


## 4. Close the database connection

// Close the connection
$rdf_database->close();

?>
</body>
</html>
