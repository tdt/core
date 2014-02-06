<?php

// ----------------------------------------------------------------------------------
// Class: testSparqlTests
// ----------------------------------------------------------------------------------

/**
 * Testcases for the SparqlParser and SparqlEngine
 *
 * @version  $Id$
 * @author Tobias Gauß <tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

require_once RDFAPI_TEST_INCLUDE_DIR . 'test/unit/Sparql/SparqlTestHelper.php';
require_once RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_SPARQLRES;

$_SESSION['sparql_result'] = array(
    1  => "res1" ,
    2  => "res2" ,
    3  => "res3" ,
    4  => "res4" ,
    5  => "res5" ,
    6  => "res6"
);


class testResultParserTests extends UnitTestCase
{
    function testResultParser()
    {
        echo "<b>ResultParser tests</b><br/>\n";
        foreach($_SESSION['sparql_result'] as $name) {
            $_SESSION['test'] = $name . ' test';
            $parser = new SparqlResultParser();

            $qs     = file_get_contents(SPARQL_TESTFILES . 'data/'   .$name . '.xml', 'r');
            $res    = file_get_contents(SPARQL_TESTFILES . 'result/' .$name . '.res', 'r');
            eval($res);

            $q      = $parser->parse($qs);

            $this->assertTrue(SparqlTestHelper::resultCheck($q,$result));
        }
    }
}
?>