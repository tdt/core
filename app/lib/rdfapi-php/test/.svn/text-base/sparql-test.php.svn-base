<?php
/**
*   Example: Sparql query on memory model
*/
require_once dirname(__FILE__) . '/config.php';
require_once RDFAPI_INCLUDE_DIR . '/model/ModelFactory.php';

$model = ModelFactory::getMemModel();
$model->load(SPARQL_TESTFILES . 'data/model9.n3');

$qs     = 'SELECT * WHERE { ?s ?p ?o}';
$result = $model->sparqlQuery($qs);

//header('Content-Type: text/html');
//echo $result . "\n";
var_dump($result);
?>