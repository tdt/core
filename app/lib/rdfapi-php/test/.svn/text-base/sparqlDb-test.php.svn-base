<?php
/**
*   Example: Sparql query on model in database
*/
$strModel = "http://xmlns.com/foaf/0.1/";

require_once 'config.php';
require_once RDFAPI_INCLUDE_DIR . '/model/ModelFactory.php';

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
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

SELECT ?o, ?o2
  { ?s foaf:name ?o . FILTER (regex(?o, '^C') && langMatches(lang(?o), 'en')).
   ?s1 foaf:name ?o2 . FILTER (! sameTerm(?o, ?o2))}
";
$qs = <<<EOT
PREFIX foaf:       <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?value
WHERE {
      {?s ?p ?value . FILTER (isIRI(?value)) }
UNION {?s ?value ?o . FILTER (isIRI(?value)) }
UNION {?value ?p ?o . FILTER (isIRI(?value)) }
      }
EOT;
$qs = <<<EOT
SELECT DISTINCT datatype(?o) as ?dt WHERE { ?s ?p ?o} LIMIT 3
EOT;
$qs= '-';
var_dump($database->sparqlQuery($qs, null));
//echo $dbModel->sparqlQuery($qs, 'HTML');
?>