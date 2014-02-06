<?php

require_once RDFAPI_INCLUDE_DIR . 'model/ModelFactory.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlParser.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngine.php';

class Dawg2Helper
{



    /**
    *   Loads DAWG2 tests and adds them to the cases session array
    *   so that they can be executed with the normal tests
    */
    public static function loadDawg2Tests()
    {
        $suiteDir = dirname(__FILE__) . '/w3c-dawg2/data-r2/';
        $suites   = array(
            'manifest-evaluation.ttl',
            'extended-manifest-evaluation.ttl',
            'manifest-syntax.ttl'
        );

        $arTests = array();
        foreach ($suites as $suite) {
            $arTests = array_merge(
                $arTests,
                self::loadSuiteFromManifest($suiteDir . $suite)
            );
        }

        //make relative paths
        $strip = dirname(__FILE__) . '/';
        foreach ($arTests as &$test) {
            foreach ($test as $id => $value) {
                if (substr($value, 0, strlen($strip)) == $strip) {
                    $test[$id] = substr($value, strlen($strip));
                }
            }
        }

        //activate it in cases.php
        $_SESSION['sparql_dawg2_tests'] = $arTests;
        $_SESSION['sparqlTestGroups']['dawg2'] = array(//'deact'=>1,
            'title'     => 'DAWG2 tests',
            'tests'     => 'sparql_dawg2_tests',
            'checkfunc' => 'resultCheck'
        );

    }//public static function loadDawg2Tests()



    /**
    *   Loads all test collections from a test suite manifest file.
    *   A test suite file is an N3 rdf data file linking to other
    *    collection manifest files.
    *
    *   @param string $strSuiteFile Test suite file
    *
    */
    public static function loadSuiteFromManifest($strSuiteFile)
    {
        //if someone knows a proper way to select all rdf collection
        //items with a single query, tell me
        $res = self::queryN3($strSuiteFile,
            'PREFIX mf: <http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#>
            SELECT ?title, ?file WHERE {
                ?x rdfs:label ?title.
                ?y rdf:first ?file.
            }'
        );

        $arTests = array();
        $dirname = dirname($strSuiteFile);
        foreach ($res as $data) {
            $file    = $dirname . '/' . $data['?file']->uri;
            $arTests = array_merge(
                $arTests,
                self::loadCollectionFromManifest($file)
            );
        }
        return $arTests;
    }//public static function loadSuiteFromManifest($strSuiteFile)



    /**
    *   Loads tests from a test collection manifest file.
    *   A collection manifest contains only tests, no links to other
    *    manifest files.
    *
    *   @param string $strCollectionFile Path of the collection file
    *
    *   @return array Array of test definitions compatible to those in
    *                  cases.php
    */
    public static function loadCollectionFromManifest($strCollectionFile)
    {
        if (!file_exists($strCollectionFile)) {
            throw new Exception('Collection file does not exist: ' . $strCollectionFile);
        }

        $arTests = self::loadEvaluationTests($strCollectionFile);
        $arTests = array_merge($arTests, self::loadSyntaxTests($strCollectionFile));
        return $arTests;
    }//public static function loadCollectionFromManifest($strCollectionFile)



    public static function loadEvaluationTests($strCollectionFile)
    {
        $res = self::queryN3($strCollectionFile,
           'PREFIX mf:     <http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#>
            PREFIX qt:     <http://www.w3.org/2001/sw/DataAccess/tests/test-query#>
            PREFIX dawgt:  <http://www.w3.org/2001/sw/DataAccess/tests/test-dawg#>
            SELECT ?test ?name ?queryFile ?dataFile ?resultFile WHERE {
                ?test rdf:type mf:QueryEvaluationTest.
                ?test mf:name ?name.
                ?test dawgt:approval dawgt:Approved.
                ?test mf:action _:action.
                    _:action qt:query ?queryFile.
                    _:action qt:data  ?dataFile.
                ?test mf:result ?resultFile.
            }'
        );

        $dirname = dirname($strCollectionFile) . '/';
        $arTests = array();
        $prefix  = self::getPrefix($strCollectionFile);

        //this is a bug in SparqlEngine and should be fixed
        if ($res === false) {
            return array();
        }

        foreach ($res as $test) {
            $name = $test['?test']->uri;
            if (substr($name, 0, 7) !== 'http://') {
                $name = $prefix . $name;
            }
            $arTests[] = array(
                'earl:name' => $name,
                'title'     => $test['?name']->label,
                'data'      => $dirname . $test['?dataFile']->uri,
                'query'     => $dirname . $test['?queryFile']->uri,
                'result'    => $dirname . $test['?resultFile']->uri,
            );
        }

        return $arTests;
    }//public static function loadEvaluationTests($strCollectionFile)



    public static function loadSyntaxTests($strCollectionFile)
    {
        $res = self::queryN3($strCollectionFile,
           'PREFIX mf:     <http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#>
            PREFIX qt:     <http://www.w3.org/2001/sw/DataAccess/tests/test-query#>
            PREFIX dawgt:  <http://www.w3.org/2001/sw/DataAccess/tests/test-dawg#>
            SELECT ?test ?name ?queryFile ?type WHERE {
                ?test rdf:type ?type.
                ?test mf:name ?name.
                ?test dawgt:approval dawgt:Approved.
                ?test mf:action ?queryFile.
                FILTER(?type = mf:NegativeSyntaxTest || ?type = mf:PositiveSyntaxTest)
            }'
        );

        $dirname = dirname($strCollectionFile) . '/';
        $arTests = array();
        $prefix  = self::getPrefix($strCollectionFile);

        //this is a bug in SparqlEngine and should be fixed
        if ($res === false) {
            return array();
        }
        foreach ($res as $test) {
            if ($test['?type']->uri == 'http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#PositiveSyntaxTest') {
                $type = 'syntax-positive';
            } else {
                $type = 'syntax-negative';
            }
            $name = $test['?test']->uri;
            if (substr($name, 0, 7) !== 'http://') {
                $name = $prefix . $name;
            }
            $arTests[] = array(
                'earl:name' => $name,
                'title'     => $test['?name']->label,
                'query'     => $dirname . $test['?queryFile']->uri,
                'type'      => $type
            );
        }

        return $arTests;
    }//public static function loadEvaluationTests($strCollectionFile)



    protected static function getPrefix($strFile)
    {
        return 'http://www.w3.org/2001/sw/DataAccess/tests/'
                . substr(
                    $strFile,
                    strpos($strFile, 'w3c-dawg2/') + 10
                );
    }//protected static function getPrefix($strFile)



    /**
    *   Executes a SPARQL query on the data written in an
    *   file containing N3-formatted RDF data
    *
    *   @param string $strN3File      Path to file
    *   @param string $strSparqlQuery SPARQL query to execute
    *
    *   @return mixed SPARQL engine query results
    */
    public static function queryN3($strN3File, $strSparqlQuery)
    {
        $graphset = ModelFactory::getDatasetMem('Dataset1');
        $graph1   = $graphset->getDefaultGraph();
        $graph1   ->load($strN3File, 'n3');

        $parser   = new SparqlParser();
        $q        = $parser->parse($strSparqlQuery);
        $engine   = SparqlEngine::factory();
        return $engine->queryModel($graphset, $q, false);
    }//public static function queryN3($strN3File, $strSparqlQuery)
}

?>