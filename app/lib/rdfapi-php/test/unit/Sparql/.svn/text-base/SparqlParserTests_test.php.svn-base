<?php
require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/casesParserFilter.php';
require_once dirname(__FILE__) . '/casesParserNested.php';
require_once dirname(__FILE__) . '/cases.php';
require_once dirname(__FILE__) . '/SparqlTestHelper.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlParser.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlEngineDb/QuerySimplifier.php';

if (isset($GLOBALS['debugTests'])) {
    require_once 'Console/Color.php';
}

/**
*   Test Sparql parser
*/
class testSparqlParserTests extends UnitTestCase
{
    /**
    *   Tests if Query::getLanguageTag() works correctly
    */
    function testQueryGetLanguageTag()
    {
        $this->assertEqual('en', Query::getLanguageTag('?x@en'));
        $this->assertEqual('en', Query::getLanguageTag('?x@en^^xsd:integer'));
        $this->assertEqual('en', Query::getLanguageTag('?x^^xsd:integer@en'));
        $this->assertEqual('en_US', Query::getLanguageTag('?x@en_US'));
    }//function testQueryGetLanguageTag()



    /**
    *   Tests if Query::getDatatype() works correctly
    */
    function testQueryGetDatatype()
    {
        $q = new Query();
        $q->addPrefix('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
        $q->addPrefix('rdf' , 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $q->addPrefix('xsd' , 'http://www.w3.org/2001/XMLSchema#');

        $this->assertNull($q->getDatatype('?name'));
        $this->assertNull($q->getDatatype('?name@en'));
        $this->assertEqual(
            'http://www.w3.org/2001/XMLSchema#integer',
            $q->getDatatype('?name^^xsd:integer')
        );
        $this->assertEqual(
            'http://www.w3.org/2001/XMLSchema#integer',
            $q->getDatatype('?name^^<http://www.w3.org/2001/XMLSchema#integer>')
        );
    }//function testQueryGetDatatype()



    function testQueryGetFullUri()
    {
        $q = new Query();
        $q->addPrefix('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
        $q->addPrefix('rdf' , 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $q->addPrefix('xsd' , 'http://www.w3.org/2001/XMLSchema#');

        $this->assertEqual('http://www.w3.org/2001/XMLSchema#integer', $q->getFullUri('xsd:integer'));
        $this->assertFalse($q->getFullUri('yyy:integer'));
        $this->assertFalse($q->getFullUri('integer'));
    }//function testQueryGetFullUri()



    function testTokenizer()
    {
        $this->assertEqual(
            array('abc', "'", 'hi', "'", "'", 'def', "'''", 'rst', "\'", "'", "'", 'xyz'),
            SparqlParser::tokenize("abc'hi''def'''rst\\'''xyz")
        );
    }//function testTokenizer()



    function testEdgeCases()
    {
        $query = <<<EOT
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX ldap: <http://purl.org/net/ldap#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX data: <ldap://ldap.seerose.biz/>
SELECT ?Attr ?Val WHERE {<ldap://ldap.seerose.biz/dc=biz,dc=seerose,ou=People,cn=Sebastian+Dietzold> ?Attr ?Val}
EOT;
        $p = new SparqlParser();
        $q = $p->parse($query);

        $query = <<<EOT
PREFIX foaf:       <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?value
WHERE {
      {?s ?p ?value . FILTER (isIRI(?value)) }
UNION {?s ?value ?o . FILTER (isIRI(?value)) }
UNION {?value ?p ?o . FILTER (isIRI(?value)) }
      }
EOT;
        $p = new SparqlParser();
        $q = $p->parse($query);
    }//function testEdgeCases()



    function testParseFilter()
    {
        //echo "<b>FilterParser tests</b><br/>\n";
        foreach ($GLOBALS['testSparqlParserTestsFilter'] as $arFilterTest) {
            list($query, $result) = $arFilterTest;

            $p = new SparqlParser();
            $q = $p->parse($query);

            $res        = $q->getResultPart();
            $constraint = $res[0]->getConstraints();
            $tree       = $constraint[0]->getTree();

            self::removeLevel($tree);
            $this->assertEqual($result, $tree);
            if ($result != $tree) {
                var_dump($tree);
                echo '----------------------' . "\n" . $query . "\n";
                echo "\n!!!!!!!!        " . SparqlTestHelper::renderTree($tree) . "\n\n";
            }
        }
    }//function testParseFilter()



    function testParseNested()
    {
        //echo "<b>Nested queries tests</b><br/>\n";
        foreach ($GLOBALS['testSparqlParserTestsNested'] as $arNestedTest) {
            list($query, $strExpected) = $arNestedTest;

            $p = new SparqlParser();
            $q = $p->parse($query);

            $qs = new SparqlEngineDb_QuerySimplifier();
            $qs->simplify($q);

            $strRendResult = SparqlTestHelper::renderResult($q);
            $this->assertEqual($strExpected, $strRendResult);
        }
    }//function testParseNested()



    /**
    *   Runs the DAWG syntax tests
    */
    function testDawg2SyntaxTests()
    {
        $parser = new SparqlParser();

        foreach ($_SESSION['sparql_dawg2_tests'] as $test) {
            if (isset($GLOBALS['debugTests']) && $GLOBALS['debugTests']) {
                echo $test['title'] . "\n";
            }
            //use syntax tests only
            if (!isset($test['type']) ||
                ($test['type'] != 'syntax-positive' &&
                $test['type'] != 'syntax-negative')
            ) {
                continue;
            }
            if (in_array($test['title'], $_SESSION['testSparqlDbTestsIgnores'])) {
                if (isset($GLOBALS['debugTests'])) {
                    echo Console_Color::convert('%y');
                    echo '  ignoring ' . $test['title'] . "\n";
                    echo Console_Color::convert('%n');
                }
                continue;
            }
            $this->signal('earl:name', $test['earl:name']);

            $qs = file_get_contents(SPARQL_TESTFILES . $test['query']);

            $this->runQueryParseTest($qs, $parser, $test['type'], $test['title']);

            //++$nCount; if ($nCount > 2) break;
        }
    }//function testDawg2SyntaxTests()



    /**
    *   Runs a parser test
    */
    protected function runQueryParseTest($strQuery, $parser, $strType, $title)
    {
        $bException = false;
        try {
            $parser->parse($strQuery);
        } catch (Exception $e) {
            $bException = true;
        }

        if ($strType == 'syntax-negative') {
            $this->assertTrue($bException, 'Query should fail to be parsed.');
            $bOk = $bException == true;
        } else if ($strType == 'syntax-positive') {
            $this->assertFalse($bException, 'Query should get parsed.');
            $bOk = $bException == false;
        }

        if (!$bOk) {
            if (isset($GLOBALS['debugTests']) && $GLOBALS['debugTests']) {
                echo Console_Color::convert('%RTest failed: ' . $title . "%n\n");
                if (isset($e)) {
                    echo $e->getMessage() . "\n";
                    //var_dump($e);
                }
                echo $strQuery . "\n";
                die();
            }
        }
    }//protected function runQueryParseTest($strQuery, $parser, $strType)



    /**
    *   Removes "level" keys from the tree array.
    *   It is an implementation detail and should not taken into account
    */
    static function removeLevel(&$tree)
    {
        if (isset($tree['level'])) {
            unset($tree['level']);
        }
        if (isset($tree['type']) && $tree['type'] == 'equation') {
            self::removeLevel($tree['operand1']);
            self::removeLevel($tree['operand2']);
        }
    }//static function removeLevel(&$tree)

}//class testSparqlParserTests extends UnitTestCase
?>