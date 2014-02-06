<?php
/**
* Prepares your system for RAP.
* Creates database tables if they don't exist yet
*/

$strConfFile = dirname(__FILE__) . '/config.php';
if (!file_exists($strConfFile)) {
    die('Please copy "test/config.php.dist" to "test/config.php" and adjust it');
}
require_once $strConfFile;
require_once RDFAPI_INCLUDE_DIR . '/model/DbStore.php';
require_once RDFAPI_INCLUDE_DIR . '/model/ModelFactory.php';

try {
    $type = DbStore::getDriver($GLOBALS['dbConf']['type']);
    DbStore::assertDriverSupported($type);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Be sure to write the driver type in the same cAsE\n";
    exit(4);
}



try {
    $database = ModelFactory::getDbStore(
        $GLOBALS['dbConf']['type'],     $GLOBALS['dbConf']['host'],
        $GLOBALS['dbConf']['database'], $GLOBALS['dbConf']['user'],
        $GLOBALS['dbConf']['password']
    );
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Maybe the database '" . $GLOBALS['dbConf']['database'] . "' does not exist?\n";
    exit(3);
}

if ($database->isSetup()) {
   echo "Database is already setup.\n";
   exit(0);
}

try {
    $database->createTables();
} catch (Exception $e) {
    //mysql doesn't complete the transaction but is ok
    if ($e->getMessage() != '') {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Something failed when creating the tables\n";
        exit(2);
    }
}


if ($database->isSetup()) {
    echo "Database has been setup.\n";
    exit(0);
} else {
    echo "Database tables have been created, but somehow it still\n"
        . " setup is incomplete. File a bug\n";
    exit(1);
}

?>