<?php
// ----------------------------------------------------------------------------------
// Class: testSparqlTests
// Run this script by executing /test/unit/sparqlTests.php
// ----------------------------------------------------------------------------------

require_once dirname(__FILE__)       . '/../../config.php';
require_once RDFAPI_TEST_INCLUDE_DIR . 'test/unit/Sparql/cases.php';
require_once RDFAPI_TEST_INCLUDE_DIR . 'test/unit/Sparql/SparqlTestHelper.php';
require_once RDFAPI_INCLUDE_DIR      . 'sparql/SparqlParser.php';
require_once RDFAPI_INCLUDE_DIR      . 'sparql/SparqlEngine.php';

/**
 * Testcases for the SparqlParser and SparqlEngine
 *
 * @version  $Id$
 * @author Tobias Gauß <tobias.gauss@web.de>
 *
 * @package unittests
 * @access public
 */
class testSparqlTests extends UnitTestCase
{
    public function __construct()
    {
        echo "<b>SparqlTests</b><br/>\n";
    }

    function testDAWGTestcases()
    {
        foreach($_SESSION['sparql_dawg_tests'] as $name) {
            $_SESSION['test'] = $name . ' test';

            $parser   = new SparqlParser();
            $graphset = ModelFactory::getDatasetMem('Dataset1');
            $graph1   = $graphset->getDefaultGraph();
            $graph1   ->load(SPARQL_TESTFILES.'data/'.$name.'.n3');
            $qs       = file_get_contents(SPARQL_TESTFILES.'query/'.$name.'.rq','r');
            $res      = file_get_contents(SPARQL_TESTFILES.'result/'.$name.'.res','r');
            eval($res);
            $q        = $parser->parse($qs);
            $engine   = SparqlEngine::factory();
            $t        = $engine->queryModel($graphset, $q, false);

            $this->assertTrue(SparqlTestHelper::resultCheck($t,$result));
        }
    }



    function testCustomTestcases()
    {
        foreach($_SESSION['sparql_custom_tests'] as $name) {
            $_SESSION['test']=$name.' test';
            $parser = new SparqlParser();
            $graphset = ModelFactory::getDatasetMem('Dataset1');
            $graph1 = $graphset->getDefaultGraph();
            $graph1->load(SPARQL_TESTFILES.'data/'.$name.'.n3');
            $qs = file_get_contents(SPARQL_TESTFILES.'query/'.$name.'.rq','r');
            $res = file_get_contents(SPARQL_TESTFILES.'result/'.$name.'.res','r');
            eval($res);
            $q = $parser->parse($qs);
            $engine = SparqlEngine::factory();
            $t = $engine->queryModel($graphset, $q,false);


            $this->assertTrue(SparqlTestHelper::resultCheck($t,$result));
        }
    }



    function testGraphset1Testcase()
    {
        $_SESSION['test']= ' Graphset1 test';
        $parser = new SparqlParser();
        $graphset = ModelFactory::getDatasetMem('Dataset1');
        $graph1 = $graphset->createGraph('http://example.org/foaf/aliceFoaf');
        $graph1->load(SPARQL_TESTFILES.'data/graphset1gr1.n3');

        $graph2 = $graphset->createGraph('http://example.org/foaf/bobFoaf');
        $graph2->load(SPARQL_TESTFILES.'data/graphset1gr2.n3');

        $qs = file_get_contents(SPARQL_TESTFILES.'query/graphset1.rq','r');

        $res = file_get_contents(SPARQL_TESTFILES.'result/graphset1.res','r');
        eval($res);
        $q = $parser->parse($qs);
        $engine = SparqlEngine::factory();
        $t = $engine->queryModel($graphset, $q,false);
        $this->assertTrue(SparqlTestHelper::resultCheck($t,$result));
    }



    function testGraphset2Testcase()
    {
        $_SESSION['test']= ' Graphset2 test';
        $parser = new SparqlParser();
        $graphset = ModelFactory::getDatasetMem('Dataset1');
        $graph1 = $graphset->createGraph('http://example.org/foaf/aliceFoaf');
        $graph1->load(SPARQL_TESTFILES.'data/graphset1gr1.n3');

        $graph2 = $graphset->createGraph('http://example.org/foaf/bobFoaf');
        $graph2->load(SPARQL_TESTFILES.'data/graphset1gr2.n3');

        $qs = file_get_contents(SPARQL_TESTFILES.'query/graphset2.rq','r');

        $res = file_get_contents(SPARQL_TESTFILES.'result/graphset2.res','r');
        eval($res);
        $q = $parser->parse($qs);
        $engine = SparqlEngine::factory();
        $t = $engine->queryModel($graphset, $q,false);

        $this->assertTrue(SparqlTestHelper::resultCheck($t,$result));
    }



    function testGraphset3Testcase()
    {
        $_SESSION['test']= ' Graphset3 test';
        $parser = new SparqlParser();
        $graphset = ModelFactory::getDatasetMem('Dataset1');
        $default  = $graphset->getDefaultGraph();
        $default->load(SPARQL_TESTFILES.'data/graphset3gr1.n3');

        $graph1 = $graphset->createGraph('urn:x-local:graph1');
        $graph1->load(SPARQL_TESTFILES.'data/graphset3gr2.n3');

        $graph2 = $graphset->createGraph('urn:x-local:graph2');
        $graph2->load(SPARQL_TESTFILES.'data/graphset3gr3.n3');

        $qs = file_get_contents(SPARQL_TESTFILES.'query/graphset3.rq','r');

        $res = file_get_contents(SPARQL_TESTFILES.'result/graphset3.res','r');
        eval($res);
        $q = $parser->parse($qs);
        $engine = SparqlEngine::factory();
        $t = $engine->queryModel($graphset, $q,false);
        $this->assertTrue(SparqlTestHelper::resultCheck($t,$result));
    }



    function testGraphset4Testcase()
    {
        $_SESSION['test']= ' Graphset4 test';
        $parser = new SparqlParser();
        $graphset = ModelFactory::getDatasetMem('Dataset1');

        $graph1 = $graphset->createGraph('http://example.org/foaf/aliceFoaf');
        $graph1->load(SPARQL_TESTFILES.'data/graphset1gr1.n3');

        $graph2 = $graphset->createGraph('http://example.org/foaf/bobFoaf');
        $graph2->load(SPARQL_TESTFILES.'data/graphset1gr2.n3');

        $qs = file_get_contents(SPARQL_TESTFILES.'query/graphset4.rq','r');
        $res = file_get_contents(SPARQL_TESTFILES.'result/graphset4.res','r');
        eval($res);
        $q = $parser->parse($qs);
        $engine = SparqlEngine::factory();
        $t = $engine->queryModel($graphset, $q,false);
        $this->assertTrue(SparqlTestHelper::resultCheck($t,$result));
    }



    function testSortTestcase()
    {
        foreach($_SESSION['sparql_sort_tests'] as $name) {
            $_SESSION['test']= $name['query']." test";
            $parser = new SparqlParser();
            $graphset = ModelFactory::getDatasetMem('Dataset1');
            $def = $graphset->getDefaultGraph();
            $def->load(SPARQL_TESTFILES.'data/'.$name['data']);
            $qs = file_get_contents(SPARQL_TESTFILES.'query/'.$name['query'].".rq",'r');
            $res = file_get_contents(SPARQL_TESTFILES.'result/'.$name['result'].".res",'r');
            eval($res);
            $q = $parser->parse($qs);
            $engine = SparqlEngine::factory();
            $t = $engine->queryModel($graphset, $q,false);
            $this->assertTrue(SparqlTestHelper::resultCheckSort($t,$result));
        }
    }



    function testlimitOffsetTestcase()
    {
        foreach($_SESSION['sparql_limitOffset_tests'] as $name){
            $_SESSION['test']= $name['query']." test";
            $parser = new SparqlParser();
            $graphset = ModelFactory::getDatasetMem('Dataset1');
            $def = $graphset->getDefaultGraph();
            $def->load(SPARQL_TESTFILES.'data/'.$name['data']);
            $qs = file_get_contents(SPARQL_TESTFILES.'query/'.$name['query'].".rq",'r');
            $res = file_get_contents(SPARQL_TESTFILES.'result/'.$name['result'].".res",'r');
            eval($res);
            $q = $parser->parse($qs);
            $engine = SparqlEngine::factory();
            $t = $engine->queryModel($graphset, $q,false);
            $this->assertTrue(SparqlTestHelper::resultCheckSort($t,$result));
        }
    }




    function testFilterTestcases()
    {
        foreach($_SESSION['sparql_filter_tests'] as $name) {
            $_SESSION['test']= $name['query']." test";
            $parser = new SparqlParser();
            $graphset = ModelFactory::getDatasetMem('Dataset1');
            $def    = $graphset->getDefaultGraph();
            $def    ->load(SPARQL_TESTFILES.'data/'.$name['data']);
            $qs     = file_get_contents(SPARQL_TESTFILES.'query/'.$name['query'].".rq",'r');
            $res    = file_get_contents(SPARQL_TESTFILES.'result/'.$name['result'].".res",'r');
            eval($res);
            $q      = $parser->parse($qs);
            $engine = SparqlEngine::factory();
            $t      = $engine->queryModel($graphset, $q, false);
            $bOk    = SparqlTestHelper::resultCheck($t,$result);
            $this->assertTrue($bOk);
            if (!$bOk) {
                echo $name['query'] . "\n";
            }
        }
    }



    function testArqTestcases()
    {
        foreach($_SESSION['sparql_arq_tests'] as $name) {
            $_SESSION['test']= $name['query']." test";
            $parser = new SparqlParser();
            $graphset = ModelFactory::getDatasetMem('Dataset1');
            $def = $graphset->getDefaultGraph();
            $def->load(SPARQL_TESTFILES.'data/'.$name['data']);
            $qs = file_get_contents(SPARQL_TESTFILES.'query/'.$name['query'].".rq",'r');
            $res = file_get_contents(SPARQL_TESTFILES.'result/'.$name['result'].".res",'r');
            eval($res);
            $q = $parser->parse($qs);
            $engine = SparqlEngine::factory();
            $t = $engine->queryModel($graphset, $q,false);
            if ($t instanceof MemModel) {
                $bOk = $t->equals($result);
            } else {
                $bOk = SparqlTestHelper::resultCheck($t,$result);
            }
            $this->assertTrue($bOk);
            if (!$bOk) {
                echo $name['query'] . "\n";
            }
        }
    }
}
?>