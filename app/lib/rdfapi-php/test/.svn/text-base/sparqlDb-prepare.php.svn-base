<?php
$strModel = "http://xmlns.com/foaf/0.1/";

require_once 'config.php';
require_once RDFAPI_INCLUDE_DIR . '/RdfAPI.php';
require_once RDFAPI_INCLUDE_DIR . '/sparql/SPARQL.php';
$database = ModelFactory::getDbStore(
    $GLOBALS['dbConf']['type'],     $GLOBALS['dbConf']['host'],
    $GLOBALS['dbConf']['database'], $GLOBALS['dbConf']['user'],
    $GLOBALS['dbConf']['password']
);

$dbModel  = $database->getModel($strModel);

if ($dbModel === false) {
    die('Database does not have a model ' . $strModel . "\n");
}

$qs = "PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT ?o
  { ?s ??type ?o }
LIMIT 3 OFFSET 2";
$prepared = $dbModel->sparqlPrepare($qs);


$result = $prepared->execute(array('type' => 'foaf:name'));
var_dump($result);

//$result = $prepared->execute(array('type' => 'foaf:mbox'));
//var_dump($result);

?>