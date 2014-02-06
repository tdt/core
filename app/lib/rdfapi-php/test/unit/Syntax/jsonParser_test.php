<?php
require_once RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_JSON;
/**
 * Tests the JsonParser
 *
 * @version  $Id $
 * @author Philipp Frischmuth <philipp@frischmuth24.de>
 *
 * @package unittests
 * @access	public
 */

class testJsonParser extends UnitTestCase
{
	var $modelString;
	
	function testJsonParser() {
		$this->UnitTestCase();
		
		GLOBAL $short_datatype;
		
		$this->modelString = '{"http://example.org/about":{"http://purl.org/dc/elements/1.1/creator":[{"value":"Anna Wilder","type":"literal"}],"http://purl.org/dc/elements/1.1/title":[{"value":"Annas Homepage","type":"literal","lang":"en"}],"http://xmlns.com/foaf/0.1/maker":[{"value":"_:person","type":"bnode"}],"http://purl.org/dc/elements/1.1/title2":[{"value":"Anns HP","type":"literal","lang":"en","datatype":"' . $short_datatype['STRING'] . '"}]},"_:person":{"http://xmlns.com/foaf/0.1/homepage":[{"value":"http://example.org/about","type":"uri"}],"http://example.com/testProp1":[{"value":"\\"double quote\\nnewline\\ttab\\rcarriage return\\\\reverse solidus"}]}}';
	}

	function testGenerateModelFromString() {
		
		$parser = new JsonParser();
		$model = new MemModel('http://example.com/');
		
		try {
			$parser->generateModelFromString($this->modelString, $model);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
		
		#echo "<pre>";
		#print_r($model);
		
		GLOBAL $short_datatype;
		$model2 = new MemModel('http://example.com/');

        // Ceate new statements and add them to the model
        $statement1 = new Statement(new Resource('http://example.org/about'),
                                    new Resource('http://purl.org/dc/elements/1.1/creator'),
                                    new Literal('Anna Wilder'));

        $statement2 = new Statement(new Resource('http://example.org/about'),
                                    new Resource("http://purl.org/dc/elements/1.1/title"),
                                    new Literal('Annas Homepage', 'en'));

        $statement3 = new Statement(new Resource('http://example.org/about'),
                                    new Resource('http://xmlns.com/foaf/0.1/maker'),
                                    new BlankNode('person'));

        $statement4 = new Statement(new BlankNode('person'),
                                    new Resource("http://xmlns.com/foaf/0.1/homepage"),
                                    new Resource('http://example.org/about'));

		$statement5 = new Statement(new Resource('http://example.org/about'),
		                            new Resource("http://purl.org/dc/elements/1.1/title2"),
		                            new Literal('Anns HP', 'en', $short_datatype['STRING']));
		
		$statement6 = new Statement(new Resource('http://example.org/about'),
		              				new Resource("http://purl.org/dc/elements/1.1/title2"),
		              				new Literal('Anns HP', 'en', $short_datatype['INTEGER']));
		
		$statement7= new Statement(new BlankNode('person'),
					 new Resource("http://example.com/testProp1"),
					 new Literal("\"double quote\nnewline\ttab\rcarriage return\\reverse solidus"));


        $model2->add($statement1);
        $model2->add($statement2);
        $model2->add($statement3);
        $model2->add($statement4);
		$model2->add($statement5);
		$model2->add($statement7);
		
		$this->assertTrue($model->containsAll($model2));
		
		$model2->remove($statement5);
		$model2->add($statement6);
		
		$this->assertFalse($model->containsAll($model2));

#echo "<pre>";
#print_r($model2);
#echo "</pre>";
	}
}
?>
