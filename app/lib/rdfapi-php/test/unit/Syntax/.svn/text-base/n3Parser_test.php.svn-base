<?php
require_once RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3;

// ----------------------------------------------------------------------------------
// Class: testN3Parser
// ----------------------------------------------------------------------------------

/**
 * Tests the N3Parser
 *
 * @version  $Id$
 * @author Tobias Gauss <tobias.gauss@web.de>
 * @author Christian Weiske <cweiske@cweiske.de?
 *
 * @package unittests
 * @access	public
 */

class testN3Parser extends UnitTestCase
{

    function testN3Parser() {
        $this->UnitTestCase();

        $_SESSION['n3TestInput']='
            @prefix p:  <http://www.example.org/personal_details#> .
            @prefix m:  <http://www.example.org/meeting_organization#> .

            <http://www.example.org/people#fred>
                p:GivenName  	"Fred";
                p:hasEmail 		<mailto:fred@example.com>;
                m:attending 	<http://meetings.example.com/cal#m1> .

            <http://meetings.example.com/cal#m1>
                m:homePage 		<http://meetings.example.com/m1/hp> .
        ';


    }



    function testIsMemmodel()
    {
        // Import Package
        $n3pars= new N3Parser();
        $model=$n3pars->parse2model($_SESSION['n3TestInput'],false);
        $this->assertIsA($model, 'memmodel');
    }



    function testParsing()
    {
        $n3pars= new N3Parser();
        $model=$n3pars->parse2model($_SESSION['n3TestInput'],false);


        $model2 = new MemModel();

        // Ceate new statements and add them to the model
        $statement1 = new Statement(new Resource("http://www.example.org/people#fred"),
                                    new Resource("http://www.example.org/personal_details#hasEmail"),
                                    new Resource("mailto:fred@example.com"));
        $statement2 = new Statement(new Resource("http://www.example.org/people#fred"),
                                    new Resource("http://www.example.org/meeting_organization#attending"),
                                    new Resource("http://meetings.example.com/cal#m1"));
        $statement3 = new Statement(new Resource("http://www.example.org/people#fred"),
                                    new Resource("http://www.example.org/personal_details#GivenName"),
                                    new Literal("Fred"));
        $statement4 = new Statement(new Resource("http://meetings.example.com/cal#m1"),
                                    new Resource("http://www.example.org/meeting_organization#homePage"),
                                    new Resource("http://meetings.example.com/m1/hp"));


        $model2->add($statement1);
        $model2->add($statement2);
        $model2->add($statement3);
        $model2->add($statement4);


        $this->assertTrue($model->containsAll($model2));
    }



    /**
    *   Test different string quotation methods
    */
    function testQuotes()
    {
        $n3 = <<<EOT
@prefix : <http://example.org/#> .

# This file uses UNIX line end conventions.

:x1 :p1 'x' .
:x2 :p2 '''x
y''' .

:x3 :p3 """x
y"""^^:someType .


EOT;
        $parser = &new N3Parser();
        $model  = &$parser->parse2model($n3, false);

        $model2 = new MemModel();
        $model2->add(
            new Statement(
                new Resource("http://example.org/#x1"),
                new Resource("http://example.org/#p1"),
                new Literal('x')
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#x2"),
                new Resource("http://example.org/#p2"),
                new Literal("x\ny")
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#x3"),
                new Resource("http://example.org/#p3"),
                new Literal("x\ny", null, 'http://example.org/#someType')
            )
        );

        //var_dump($model->triples, $model2->triples);
        $this->assertEqual(3, $model->size());
        $this->assertTrue($model->containsAll($model2));
    }//function testQuotes()



    function testPrefixNotDeclared()
    {
        $rdfInput='
        @prefix m:  <http://www.example.org/meeting_organization#>.

        <http://www.example.org/people#fred>
            p:GivenName  	"Fred";
            p:hasEmail 		<mailto:fred@example.com>;
            m:attending 	<http://meetings.example.com/cal#m1> .
        ';

        $n3pars= new N3Parser();
        $model=$n3pars->parse2model($rdfInput,false);
            //var_dump($model);
        $this->assertErrorPattern('[Prefix not declared: p:]');
    }



    function testLoneSemicolon()
    {
        $n3 = '<a> <b> <c> ; .';
        $parser = &new N3Parser();
        $model = &$parser->parse2model($n3, false);
        $this->assertEqual(1, $model->size());
        $this->assertNoErrors();
    }



    function testTightClosingList()
    {
        $n3 = '@prefix : <http://www.w3.org/2001/sw/DataAccess/tests/data-r2/syntax-sparql4/manifest#> .
                @prefix mf:     <http://www.w3.org/2001/sw/DataAccess/tests/test-manifest#> .
                <>  mf:entries ( mf:syn-09) .';
        $parser = &new N3Parser();
        $model = &$parser->parse2model($n3, false);
        //if bug occured, the parser would be in an endless loop
    }



    /**
    *   Check number parsing
    *   @see http://www.w3.org/2000/10/swap/grammar/n3-report.html#node
    */
    function testNumbers()
    {
        $n3 = '@prefix : <http://example.org/#> .
            :foo :bar 0.7 .
            :foo :bar 42 .
            :foo :bar 10e6 .

            :foo :bar -0.7 .
            :foo :bar -42 .
            :foo :bar -12E-6 .
            ';
        $parser = &new N3Parser();

        $model  = &$parser->parse2model($n3, false);

        $model2 = new MemModel();
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(0.7, null, XML_SCHEMA . 'double')
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(42, null, XML_SCHEMA . 'integer')
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(10e6, null, XML_SCHEMA . 'double')
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(-0.7, null, XML_SCHEMA . 'double')
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(-42, null, XML_SCHEMA . 'integer')
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(-12E-6, null, XML_SCHEMA . 'double')
            )
        );

        $this->assertEqual(6, $model->size());
        $this->assertTrue($model->containsAll($model2));
    }//function testNumbers()



    function testBooleans()
    {
        $n3 = '@prefix : <http://example.org/#> .
            :foo :bar @true .
            :foo :bar @false .
            ';
        $parser = &new N3Parser();
        //$parser->debug = true;
        $model  = &$parser->parse2model($n3, false);

        $model2 = new MemModel();
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(true, null, XML_SCHEMA . 'boolean')
            )
        );
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://example.org/#bar"),
                new Literal(false, null, XML_SCHEMA . 'boolean')
            )
        );

        //var_dump($model->triples);
        $this->assertEqual(2, $model->size());
        $this->assertTrue($model->containsAll($model2));
    }//function testBooleans()



    function testA()
    {
        $n3 = '@prefix : <http://example.org/#> .
            :foo a :bar .
            ';
        $parser = &new N3Parser();
        //$parser->debug = true;
        $model  = &$parser->parse2model($n3, false);

        $model2 = new MemModel();
        $model2->add(
            new Statement(
                new Resource("http://example.org/#foo"),
                new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"),
                new Resource("http://example.org/#bar")
            )
        );

        //var_dump($model->triples);
        $this->assertEqual(1, $model->size());
        $this->assertTrue($model->containsAll($model2));
    }//function testA()
}
?>
