<?php
require_once RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_JSON;
/**
 * Tests the JsonSerializer
 *
 * @version  $Id $
 * @author Philipp Frischmuth <philipp@frischmuth24.de>
 *
 * @package unittests
 * @access	public
 */
class testJsonSerializer extends UnitTestCase {

	var $model;

	function testJsonSerializer() {
		
		$this->model = new MemModel();
		
		$res1 = new Resource('http://example.com/res1');
		$res2 = new Resource('http://example.com/res2');
		$res3 = new Resource('http://example.com/res3');
		
		$bn1 = new BlankNode('test1');
		$bn2 = new BlankNode('test2');
		
		$literal1 = new Literal('test literal');
		$literal2 = new Literal('test literal', 'en');
		$literal3 = new Literal('test literal', null, 'http://www.w3.org/2001/XMLSchema#string');
		
		// test literals with tabs and newlines and double quotes
		$literal4 = new Literal("test literal\ttab\nnewline\"double quote\rcarriage return\fformfeed\bbackspace\\reverse solidus", null, 'http://www.w3.org/2001/XMLSchema#string');
		
		$stm1 = new Statement($res1, $res2, $res3);
		$stm2 = new Statement($res1, $res2, $bn1);
		$stm3 = new Statement($res1, $res2, $literal1);
		$stm4 = new Statement($res1, $res2, $literal2);
		$stm5 = new Statement($res1, $res2, $literal3);
		$stm6 = new Statement($bn1, $res2, $bn2);
		$stm7 = new Statement($res2, $res1, $literal4);
		
		$this->model->add($stm1);
		$this->model->add($stm2);
		$this->model->add($stm3);
		$this->model->add($stm4);
		$this->model->add($stm5);
		$this->model->add($stm6);
		$this->model->add($stm7);
	}
	
	function testSerialize() {
		
		$ser = new JsonSerializer();
	
		$jsonString = $ser->serialize($this->model);
		$memModel = new MemModel();
		$memModel->loadFromString($jsonString, 'json');
				
		$this->assertTrue($this->model->equals($memModel));
	}
}
?>
