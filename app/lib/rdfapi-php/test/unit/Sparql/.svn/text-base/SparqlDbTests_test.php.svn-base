<?php
/**
*   SparqlEngineDb unit tests
*   Run this script by executing /test/unit/sparqldbTests.php
*/
require_once dirname(__FILE__) . '/../../config.php';
require_once RDFAPI_TEST_INCLUDE_DIR . 'test/unit/Sparql/cases.php';
require_once RDFAPI_TEST_INCLUDE_DIR . 'test/unit/Sparql/SparqlTestHelper.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlParser.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngine.php';
require_once RDFAPI_INCLUDE_DIR . 'dataset/NamedGraphMem.php';
require_once RDFAPI_INCLUDE_DIR . 'syntax/SparqlResultParser.php';
require_once RDFAPI_INCLUDE_DIR . 'syntax/N3Parser.php';

if (isset($GLOBALS['debugTests'])) {
    require_once 'Console/Color.php';
}

//SparqlTestHelper::loadDawg2Tests();


class testSparqlDbTests extends UnitTestCase
{
    protected static $strModelUri = 'unittest-model';



    public function testAllTestgroupsNoReload()
    {
        echo "<b>SparqlDbTests</b><br/>\n";
        //prepare
        $parser   = new SparqlParser();
        $strLastDataFile = null;

        foreach ($_SESSION['sparqlTestGroups'] as $arGroup) {
            if (isset($arGroup['deact'])) continue;
//echo count($_SESSION[$arGroup['tests']]) . " tests\n";

            foreach ($_SESSION[$arGroup['tests']] as $name) {

                if (isset($name['type']) &&
                    ($name['type'] == 'syntax-negative' || $name['type'] == 'syntax-positive')
                ) {
                    //skip syntax tests; they are run in SparqlParserTests
                    continue;
                }

                $checkfunc  = $arGroup['checkfunc'];
                $fileData   = null;
                $fileResult = null;
                $fileQuery  = null;

                if (is_array($name)) {
                    if (isset($name['data'])) {
                        if (!file_exists(SPARQL_TESTFILES . $name['data'])) {
                            $fileData = 'data/' . $name['data'];
                        } else {
                            $fileData = $name['data'];
                        }
                    }

                    if (!file_exists(SPARQL_TESTFILES . $name['query'])) {
                        $fileQuery = 'query/'  . $name['query']  . '.rq';
                    } else {
                        $fileQuery = $name['query'];
                    }

                    if (isset($name['result'])) {
                        if (!file_exists(SPARQL_TESTFILES . $name['result'])) {
                            $fileResult = 'result/' . $name['result'] . '.res';
                        } else {
                            $fileResult = $name['result'];
                        }
                    }

                    if (isset($name['title'])) {
                        $title = $name['title'];
                    } else {
                        $title = $name['query'];
                    }
                } else {
                    $fileData   = 'data/'   . $name . '.n3';
                    $fileQuery  = 'query/'  . $name . '.rq';
                    $fileResult = 'result/' . $name . '.res';
                    $title      = $name;
                }

                if (in_array($title, $_SESSION['testSparqlDbTestsIgnores'])) {
                    if (isset($GLOBALS['debugTests'])) {
                        echo Console_Color::convert('%y');
                        echo '  ignoring ' . $title . "\n";
                        echo Console_Color::convert('%n');
                    }
                    continue;
                }
//echo '  ' . $title . "\n";
                $_SESSION['test'] = $title . ' test';
                $e = null;

                if (isset($name['earl:name'])) {
                    //fix some weird issue with simpletest
                    $earlname = $name['earl:name'];
                    $this->signal('earl:name', $earlname);
                }

                if ($fileData != null && $fileData != $strLastDataFile) {
                    //re-use database if not changed
                    list($database, $dbModel) = $this->prepareDatabase();
                    //import statements into database
                    $dbModel  ->load(SPARQL_TESTFILES . $fileData, 'n3');
                    $strLastDataFile = $fileData;
                }

                $qs = file_get_contents(SPARQL_TESTFILES . $fileQuery);

                if ($fileResult !== null) {
                    $res = file_get_contents(SPARQL_TESTFILES . $fileResult);

                    if (substr($fileResult, -4) == '.srx') {
                        //Sparql XML result
                        $resParser = new SparqlResultParser();
                        $result    = $resParser->parse($res);
                    } else if (substr($fileResult, -4) == '.rdf') {
                        //same format as .ttl, but serialized as xml
                        //rdf xml sorted
                        $resModel = new MemModel();
                        $resModel->load(SPARQL_TESTFILES . $fileResult, 'rdf');
                        $result   = SparqlTestHelper::convertModelToResultArray($resModel);
                        unset($resModel);
                        $checkfunc = 'resultCheckSort';
                    } else if (substr($fileResult, -4) == '.res') {
                        //our own php code
                        unset($result);
                        eval($res);
                    } else if (substr($fileResult, -4) == '.ttl') {
                        //N3
                        $resModel = new MemModel();
                        $resModel->load(SPARQL_TESTFILES . $fileResult, 'n3');
                        $result   = SparqlTestHelper::convertModelToResultArray($resModel);
                        unset($resModel);
                    } else {
                        throw new Exception('Unknown result format in ' . $fileResult);
                    }
                }

                try {
                    $q = $parser->parse($qs);
                } catch (Exception $e) {
                    //normal query failed to be parsed
                    $this->assertTrue(false, 'Query failed to be parsed');
                    if (!isset($GLOBALS['debugTests'])) {
                        //echo '  ' . $title . "\n";
                    } else {
                        echo Console_Color::convert('%RTest failed: ' . $title . "%n\n");
                        if (isset($e)) {
                            echo $e->getMessage() . "\n";
                            //var_dump($e);
                        }
                        echo $strQuery . "\n";
                        die();
                    }
                }

                try {
                    $t = $dbModel->sparqlQuery($qs);

                    if ($t instanceof MemModel) {
                        $bOk = $t->equals($result);
                    } else {
                        $bOk = SparqlTestHelper::$checkfunc($t, $result);
                    }
                    $this->assertTrue($bOk);
                } catch (Exception $e) {
                    $bOk = false;
                    $t = null;
                    //an exception is an error
                    if (isset($GLOBALS['debugTests'])) {
                        var_dump($e->getMessage());
                    }
                    $this->assertTrue(false);
                }

                if (!$bOk) {
                    if (!isset($GLOBALS['debugTests'])) {
                        //echo '  ' . $title . "\n";
                    } else {
                        echo Console_Color::convert('%RTest failed: ' . $title . "%n\n");
                        if ($e != null) {
                            echo get_class($e) . ': ' . $e->getMessage() . "\n";
                        }
                        echo ' Data: ' . $fileData . "\n";
                        echo 'Query string: ' . $qs . "\n";
                        echo "Expected:\n";
                        echo Console_Color::convert('%p');
                        var_dump($result);
                        echo Console_Color::convert('%n');
                        echo "Result:\n";
                        echo Console_Color::convert('%r');
                        var_dump($t);
                        echo Console_Color::convert('%n');
                        //var_dump($q);
                        die();
                    }
                }
/**/
            }
//            echo $arGroup['title'] . " done\n";
        }
    }//public function testAllTestgroupsNoReload()



    /**
    *   Instantiates the database object and clears
    *   any existing statements to have a fresh place
    *   for a unit test.
    *
    *   @return array       array($database, $dbModel)
    */
    protected function prepareDatabase()
    {
        $database = ModelFactory::getDbStore(
            $GLOBALS['dbConf']['type'],
            $GLOBALS['dbConf']['host'],
            $GLOBALS['dbConf']['database'],
            $GLOBALS['dbConf']['user'],
            $GLOBALS['dbConf']['password']
        );
        if ($database->modelExists(self::$strModelUri)) {
            //need to remove model
            $database->removeNamedGraphDb(self::$strModelUri);
        }
        $dbModel  = $database->getNewModel(self::$strModelUri);

        if (!$dbModel instanceof DbModel) {
            throw new Exception('Error creating new model for SparqlEngineDb tests: ' . $dbModel);
        }
        return array($database, $dbModel);
    }//protected function prepareDatabase()

}//class testSparqlDbTests extends UnitTestCase
?>