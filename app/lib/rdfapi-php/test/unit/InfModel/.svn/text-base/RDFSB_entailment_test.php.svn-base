<?php

// ----------------------------------------------------------------------------------
// Class: testJenaInfmodelTests
// ----------------------------------------------------------------------------------

/**
 * Tests the basic operations of the RDFS & RDFSB Inf Models
 *
 * @version  $Id$
 * @author Daniel Westphal	<mail at d-westphal dot de>
 *
 * @package unittests
 * @access	public
 */
include_once( RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_N3);
 
class testRDFSBEntailmentTests extends UnitTestCase {
 	
	/**
	* 
	*/
	function test1()
	{
		$inf= new RDFSBModel('http://myRDFSFModel.com');
		$result= new MemModel();
		$parser= new N3Parser();

		$inf->addModel($parser->parse2model(
		'
		<http://example.org/baz1> <http://example.org/bat> <http://example.org/baz2> .
		<http://example.org/bat> <http://www.w3.org/2000/01/rdf-schema#subPropertyOf> <http://example.org/bas> .
		'));
		
		$result->addModel($parser->parse2model(
		'
		@prefix ns0: <http://example.org/> .
		@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
		ns0:baz1 ns0:bas ns0:baz2 ; ns0:bat ns0:baz2 .
		ns0:bat rdfs:subPropertyOf ns0:bas .
		'));
		
		$this->assertTrue($inf->equals($result));
		
//next test

		$inf->add(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#domain'),new Resource('http://example.org/Domain1')));
		$inf->add(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#range'),new Resource('http://example.org/Range1')));
		
		$result=new MemModel();
		$result->addModel($parser->parse2model(
		'
			@prefix ns0: <http://example.org/> .
			@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
			@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
			ns0:baz1 ns0:bas ns0:baz2 ; ns0:bat ns0:baz2 ; a ns0:Domain1 .
			ns0:bat rdfs:domain ns0:Domain1 ; rdfs:range ns0:Range1 ; rdfs:subPropertyOf ns0:bas .
			ns0:baz2 a ns0:Range1 .
		'));

		$this->assertTrue($inf->equals($result));
		
// next test
		
		$inf->add(new Statement(new Resource('http://example.org/bas'),new Resource('http://www.w3.org/2000/01/rdf-schema#domain'),new Resource('http://example.org/Domain2')));
		$inf->add(new Statement(new Resource('http://example.org/bas'),new Resource('http://www.w3.org/2000/01/rdf-schema#range'),new Resource('http://example.org/Range2')));

		$result=new MemModel();
		$result->addModel($parser->parse2model(
		'
		@prefix ns0: <http://example.org/> .
		@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
		@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		ns0:baz1 ns0:bas ns0:baz2 ; ns0:bat ns0:baz2 ; a ns0:Domain1 ,  ns0:Domain2 .
		ns0:bat rdfs:domain ns0:Domain1 ; rdfs:range ns0:Range1 ; rdfs:subPropertyOf ns0:bas .
		ns0:bas rdfs:domain ns0:Domain2 ; rdfs:range ns0:Range2 .
		ns0:baz2 a ns0:Range1 ,  ns0:Range2 .
		'));
		
		$this->assertTrue($inf->equals($result));

//next test

		$inf->add(new Statement(new Resource('http://example.org/Domain2'),new Resource('http://www.w3.org/2000/01/rdf-schema#subClassOf'),new Resource('http://example.org/Domain3')));
		$inf->add(new Statement(new Resource('http://example.org/Domain3'),new Resource('http://www.w3.org/2000/01/rdf-schema#subClassOf'),new Resource('http://example.org/Domain2')));
		
		$result=new MemModel();
		$result->addModel($parser->parse2model(
		'
		@prefix ns0: <http://example.org/> .
		@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
		@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		ns0:baz1 ns0:bas ns0:baz2 ; ns0:bat ns0:baz2 ; a ns0:Domain1 ,  ns0:Domain2 ,  ns0:Domain3 .
		ns0:bat rdfs:domain ns0:Domain1 ; rdfs:range ns0:Range1 ; rdfs:subPropertyOf ns0:bas .
		ns0:bas rdfs:domain ns0:Domain2 ; rdfs:range ns0:Range2 .
		ns0:baz2 a ns0:Range1 ,  ns0:Range2 .
		ns0:Domain3 rdfs:subClassOf ns0:Domain2 .
		ns0:Domain2 rdfs:subClassOf ns0:Domain3 .
		'));
		
		$this->assertTrue($inf->equals($result));
		
//next test

		$inf->add(new Statement(new Resource('http://example.org/Range3'),new Resource('http://www.w3.org/2002/07/owl#sameAs'),new Resource('http://example.org/Range2')));

		$result=new MemModel();
		$result->addModel($parser->parse2model(
		'
		@prefix ns0: <http://example.org/> .
		@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
		@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		@prefix owl: <http://www.w3.org/2002/07/owl#> .
		ns0:baz1 ns0:bas ns0:baz2 ; ns0:bat ns0:baz2 ; a ns0:Domain1 ,  ns0:Domain2 ,  ns0:Domain3 .
		ns0:baz2 a ns0:Range1 ,  ns0:Range2 ,  ns0:Range3 .
		ns0:bat rdfs:domain ns0:Domain1 ; rdfs:range ns0:Range1 ; rdfs:subPropertyOf ns0:bas .
		ns0:bas rdfs:domain ns0:Domain2 ; rdfs:range ns0:Range2 .
		ns0:Range3 owl:sameAs ns0:Range2 .
		ns0:Domain3 rdfs:subClassOf ns0:Domain2 .
		ns0:Domain2 rdfs:subClassOf ns0:Domain3 .
		'));
		
		$this->assertTrue($inf->equals($result));
		
//next test

		$findResult=$inf->find(new Resource('http://example.org/baz2'),null,null);

		$result=new MemModel();
		$result->addModel($parser->parse2model(
		'
		@prefix ns0: <http://example.org/> .
		@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		ns0:baz2 a ns0:Range1 ,  ns0:Range2 ,  ns0:Range3 .
		'));

		$this->assertTrue($findResult->equals($result));
		
//text test

		$inf->remove(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#subPropertyOf'),new Resource('http://example.org/bas')));
		
		$result=new MemModel();
		$result->addModel($parser->parse2model(
		'
		@prefix ns0: <http://example.org/> .
		@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
		@prefix owl: <http://www.w3.org/2002/07/owl#> .
		@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
		ns0:baz1 ns0:bat ns0:baz2 ; a ns0:Domain1 .
		ns0:bas rdfs:domain ns0:Domain2 ; rdfs:range ns0:Range2 .
		ns0:bat rdfs:domain ns0:Domain1 ; rdfs:range ns0:Range1 .
		ns0:baz2 a ns0:Range1 .
		ns0:Range3 owl:sameAs ns0:Range2 .
		ns0:Domain2 rdfs:subClassOf ns0:Domain3 .
		ns0:Domain3 rdfs:subClassOf ns0:Domain2 .
		'));
		
		$this->assertTrue($inf->equals($result));
	}
	
}
?>