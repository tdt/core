<?php
require_once RDFAPI_INCLUDE_DIR . 'model/MemModel.php';
require_once RDFAPI_INCLUDE_DIR . 'syntax/N3Serializer.php';
require_once RDFAPI_INCLUDE_DIR . 'syntax/N3Parser.php';

/**
 * Unit tests for N3Serializer
 *
 * @version  $Id$
 * @author Christian Weiske <cweiske@cweiske.de>
 *
 * @package unittests
 */
class testN3SerializerTests extends UnitTestCase
{
    function testSimple()
    {
        $mod = new MemModel();
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Resource("mailto:fred@example.com")
        ));

        $ser = new N3Serializer();
        $str = $ser->serialize($mod);

        $this->assertTrue(strpos($str, '<http://example.org/>') > 0);
        $this->assertTrue(strpos($str, '@prefix') !== false);
        $this->assertTrue(strpos($str, ':foo') > 0);
        $this->assertTrue(strpos($str, ':bar') > 0);
        $this->assertTrue(strpos($str, 'fred@example.com') > 0);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->assertEqual($mod->size(), $mod2->size(), 'Original model size and loaded model size should equal');
        $this->assertTrue($mod->containsAll($mod2), 'Original model should contain all triples of loaded model');
        $this->assertTrue($mod2->containsAll($mod), 'Loaded model should contain all triples of original model');
    }//function testSimple()



    function testStringsSimple()
    {
        $mod = new MemModel();
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal('testliteral')
        ));
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal("test''literal")
        ));
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal("test\"\"literal")
        ));
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal("test\nliteral")
        ));
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal("test\"\nliteral")
        ));
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal("test'\nliteral")
        ));


        $ser = new N3Serializer();
        $str = $ser->serialize($mod);

        $this->assertTrue(strpos($str, 'testliteral') > 0);
        $this->assertTrue(strpos($str, "test''literal") > 0);
        $this->assertTrue(strpos($str, 'test""literal') > 0);
        $this->assertTrue(strpos($str, "test\nliteral") > 0);
        $this->assertTrue(strpos($str, "'''test\"\nliteral'''") > 0);
        $this->assertTrue(strpos($str, '"""test\'' . "\n" . 'literal"""') > 0);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->assertEqual($mod->size(), $mod2->size(), 'Original model size and loaded model size should equal');
        $this->assertTrue($mod->containsAll($mod2), 'Original model should contain all triples of loaded model');
        $this->assertTrue($mod2->containsAll($mod), 'Loaded model should contain all triples of original model');
    }


    function testStringsAdvanced()
    {
        $mod = new MemModel();
        //up to now, we didn't trick the serializer
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal("test'\"literal")
        ));
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Literal("test'\"\nliteral")
        ));


        $ser = new N3Serializer();
        $str = $ser->serialize($mod);

        $this->assertTrue(strpos($str, 'test\\\'\\"literal') > 0);
        $this->assertTrue(strpos($str, 'test\\\'\\"' . "\n" . 'literal') > 0);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->assertEqual($mod->size(), $mod2->size(), 'Original model size and loaded model size should equal');
        $this->assertTrue($mod->containsAll($mod2), 'Original model should contain all triples of loaded model');
        $this->assertTrue($mod2->containsAll($mod), 'Loaded model should contain all triples of original model');
    }



    function testNoNSPrefix()
    {
        $mod = new MemModel();
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Resource("mailto:fred@example.com")
        ));

        $ser = new N3Serializer();
        $ser->addNoNSPrefix('mailto:');
        $str = $ser->serialize($mod);

        $this->assertTrue(strpos($str, '<http://example.org/>') > 0);
        $this->assertTrue(strpos($str, '@prefix') !== false);
        $this->assertTrue(strpos($str, ':foo') > 0);
        $this->assertTrue(strpos($str, ':bar') > 0);
        $this->assertTrue(strpos($str, '<mailto:fred@example.com>') > 0);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->assertEqual($mod->size(), $mod2->size(), 'Original model size and loaded model size should equal');
        $this->assertTrue($mod->containsAll($mod2), 'Original model should contain all triples of loaded model');
        $this->assertTrue($mod2->containsAll($mod), 'Loaded model should contain all triples of original model');
    }



    function testNoNSPrefix2()
    {
        $mod = new MemModel();
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            new Resource("mailto:fred@example.com")
        ));

        $ser = new N3Serializer();
        $ser->addNoNSPrefix('http://example.org/');
        $ser->addNoNSPrefix('mailto:');
        $str = $ser->serialize($mod);

        $this->assertTrue(strpos($str, '@prefix') === false);
        $this->assertTrue(strpos($str, '<http://example.org/foo>') > 0);
        $this->assertTrue(strpos($str, '<http://example.org/bar>') > 0);
        $this->assertTrue(strpos($str, '<mailto:fred@example.com>') > 0);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->assertEqual($mod->size(), $mod2->size(), 'Original model size and loaded model size should equal');
        $this->assertTrue($mod->containsAll($mod2), 'Original model should contain all triples of loaded model');
        $this->assertTrue($mod2->containsAll($mod), 'Loaded model should contain all triples of original model');
    }



    function testCompressOneBlank()
    {
        $mod = new MemModel();
        $b1  = new BlankNode($mod);
        $mod->add(new Statement(
            $b1,
            new Resource("http://example.org/bar1"),
            new Literal('baz')
        ));

        $ser = new N3Serializer();
        $ser->setCompress(true);
        $ser->setNest(true);
        $str = $ser->serialize($mod);
        //var_dump($str);//, $mod2->triples);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);

        $this->assertEqual($mod->size(), $mod2->size(), 'Original model size and loaded model size should equal');
        $this->compareModelsIgnoringBlankNodes($mod, $mod2);
    }



    function testCompressTwoBlanks()
    {
        $mod = new MemModel();
        $b1  = new BlankNode($mod);
        $b2  = new BlankNode($mod);
        $mod->add(new Statement(
            $b1,
            new Resource("http://example.org/bar1"),
            new Literal('baz1')
        ));
        $mod->add(new Statement(
            $b2,
            new Resource("http://example.org/bar2"),
            new Literal('baz2')
        ));

        $ser = new N3Serializer();
        $ser->setCompress(true);
        $ser->setNest(true);
        $str = $ser->serialize($mod);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->compareModelsIgnoringBlankNodes($mod, $mod2);
    }



    function testCompressBlankAtEnd()
    {
        $mod = new MemModel();
        $b3  = new BlankNode($mod);
        $mod->add(new Statement(
            new Resource('http://example.org/foo'),
            new Resource("http://example.org/bar2"),
            $b3
        ));

        $ser = new N3Serializer();
        $ser->setCompress(true);
        $str = $ser->serialize($mod);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->compareModelsIgnoringBlankNodes($mod, $mod2);
    }



    function testNestSimple()
    {
        $mod = new MemModel();
        $b3  = new BlankNode($mod);
        $mod->add(new Statement(
            new Resource('http://example.org/foo'),
            new Resource("http://example.org/bar2"),
            $b3
        ));
        $mod->add(new Statement(
            $b3,
            new Resource("http://example.org/bar2"),
            new Literal('hohoho')
        ));

        $ser = new N3Serializer();
        $ser->setCompress(true);
        $ser->setNest(true);
        $str = $ser->serialize($mod);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->compareModelsIgnoringBlankNodes($mod, $mod2);
    }



    function testNestSimpleBlankStart()
    {
        $mod = new MemModel();
        $b1  = new BlankNode($mod);
        $b3  = new BlankNode($mod);
        $mod->add(new Statement(
            $b1,
            new Resource("http://example.org/bar2"),
            $b3
        ));
        $mod->add(new Statement(
            $b3,
            new Resource("http://example.org/bar2"),
            new Literal('hohoho')
        ));

        $ser = new N3Serializer();
        $ser->setCompress(true);
        $ser->setNest(true);
        $str = $ser->serialize($mod);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str);//, $mod2->triples);

        $this->compareModelsIgnoringBlankNodes($mod, $mod2);
    }



    function testNestDeeply()
    {
        $mod  = new MemModel();
        $b100 = new BlankNode($mod);
        $b110 = new BlankNode($mod);
        $b120 = new BlankNode($mod);
        $b111 = new BlankNode($mod);
        $mod->add(new Statement(
            new Resource("http://example.org/foo"),
            new Resource("http://example.org/bar"),
            $b100
        ));
            $mod->add(new Statement(
                $b100,
                new Resource("http://example.org/bar2"),
                $b110
            ));
                $mod->add(new Statement(
                    $b110,
                    new Resource("http://example.org/bar4"),
                    $b111
                ));
            $mod->add(new Statement(
                $b100,
                new Resource("http://example.org/bar3"),
                $b120
            ));

        $ser = new N3Serializer();
        $ser->setCompress(true);
        $ser->setNest(true);
        $str = $ser->serialize($mod);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->compareModelsIgnoringBlankNodes($mod, $mod2);
    }



    function testNestBlankAtEnd()
    {
        $mod = new MemModel();
        $b3  = new BlankNode($mod);
        $mod->add(new Statement(
            new Resource('http://example.org/foo'),
            new Resource("http://example.org/bar2"),
            $b3
        ));

        $ser = new N3Serializer();
        $ser->setCompress(true);
        $ser->setNest(true);
        $str = $ser->serialize($mod);

        //test if it can be loaded
        $par = new N3Parser();
        $mod2 = $par->parse2model($str, false);
        //var_dump($str, $mod2->triples);

        $this->compareModelsIgnoringBlankNodes($mod, $mod2);
    }









    function compareModelsIgnoringBlankNodes($mod1, $mod2)
    {
        $this->assertEqual($mod1->size(), $mod2->size(), 'Original model size and loaded model size should equal');

        foreach ($mod1->triples as &$triple) {
            $s = $p = $o = null;
            if (!$triple->subj instanceof BlankNode) {
                $s = $triple->subj;
            }
            if (!$triple->pred instanceof BlankNode) {
                $p = $triple->pred;
            }
            if (!$triple->obj instanceof BlankNode) {
                $o = $triple->obj;
            }
            $res = $mod2->find($s, $p, $o);
            $this->assertTrue($res->size() > 0);
        }

        foreach ($mod2->triples as &$triple) {
            $s = $p = $o = null;
            if (!$triple->subj instanceof BlankNode) {
                $s = $triple->subj;
            }
            if (!$triple->pred instanceof BlankNode) {
                $p = $triple->pred;
            }
            if (!$triple->obj instanceof BlankNode) {
                $o = $triple->obj;
            }
            $res = $mod1->find($s, $p, $o);
            $this->assertTrue($res->size() > 0);
        }
    }//function compareModelsIgnoringBlankNodes($mod1, $mod2)

}
?>