<?php
// ----------------------------------------------------------------------------------
// Class: ResModel_Property_tests
// ----------------------------------------------------------------------------------

/**
 * 
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */
 
 
 class testResModel_Property_tests extends UnitTestCase {
 	
 	/**
	* tests getProperty()
	*/
 	function testGetProptertyTest(){
 		$_SESSION['test']='ResModel getProperty test';
 		$model1=new MemModel();
 		$needle=new Statement(new Resource('http://www.example.org/needle'),new Resource('http://www.example.org/pred3'),new Resource('http://www.example.org/ob'));
		$model1->add($needle);
		
		$stat=new Statement(new Resource('http://www.example.org/needle'),new Resource('http://www.example.org/pred'),new Literal('Object'));
		$model1->add($stat);
		
		$resmodel=new ResModel($model1);
		$subject=new ResResource('http://www.example.org/needle');
		$property=new ResResource('http://www.example.org/pred');
		
		$res=$resmodel->getProperty($subject,$property);
 		$this->assertEqual('Triple(Resource("http://www.example.org/needle"), Resource("http://www.example.org/pred"), Literal("Object"))',$res->toString());
 	    $model1->close();
 	}
 	
 	/**
	* tests createProperty() and createResource
	*/
 	function testCreateProptertyCreateResourceTest(){
 		$_SESSION['test']='ResModel createProperty createResource test';
 		$model1=new MemModel();
 		$needle=new Statement(new Resource('http://www.example.org/needle'),new Resource('http://www.example.org/pred3'),new Resource('http://www.example.org/ob'));
		$model1->add($needle);
		$resmodel=new ResModel($model1);
		$resresource=$resmodel->createResource('http://www.example.org/testresource');
		$prop=$resmodel->createProperty('http://www.example.org/pred');		
		$resresource->addProperty($prop,new ResLiteral('Object'));		
		$subject=new ResResource('http://www.example.org/testresource');
		$property=new ResResource('http://www.example.org/pred');		
		$res=$resmodel->getProperty($subject,$property);
 		$this->assertEqual('Triple(Resource("http://www.example.org/testresource"), Resource("http://www.example.org/pred"), Literal("Object"))',$res->toString());
 	    $model1->close();
 	}
 	
 	/**
	* tests listSubjectsWithProperty()
	*/
 	function testlistSubjectsWithPropertyTest(){
 		$_SESSION['test']='ResModel listSubjectsWithProperty test';
 		$model1=new MemModel();
 		$needle=new Statement(new Resource('http://www.example.org/needle'),new Resource('http://www.example.org/pred'),new Resource('http://www.example.org/ob'));
		$model1->add($needle);
		$resmodel=new ResModel($model1);
		$resresource=$resmodel->createResource('http://www.example.org/testresource');
		$prop=$resmodel->createProperty('http://www.example.org/pred');		
		$resresource->addProperty($prop,new ResLiteral('Object'));		
		$property=new ResResource('http://www.example.org/pred');		
		
		$res=$resmodel->listSubjectsWithProperty($property);
		
		$this->assertEqual(1,count($res));
 	    
 		$model1->close();
 	}
 	
 	/**
	* tests _resNode2Node()
	*/
 	function test_resNode2NodeTest(){
 		$_SESSION['test']='ResModel _resNode2Node test';
 		$model1=new MemModel();
		$resmodel=new ResModel($model1);
		$resLit=new ResLiteral('Literal','DE');
		$resLit->setDatatype('type');			
		$result=$resmodel->_resNode2Node($resLit);
		$this->assertIsA($result, 'Literal');
		$this->assertEqual($result->getLanguage(),'DE');
		$this->assertEqual($result->getDatatype(),'type');
 		$model1->close();
 	}
 	
 	/**
	* tests _node2ResNode()
	*/
 	function test_node2ResNodeTest(){
 		$_SESSION['test']='ResModel _node2ResNode test';
 		$model1=new MemModel();
		$resmodel=new ResModel($model1);
		$literal=new Literal('Literal','DE');
		$literal->setDatatype('type');
		$result=$resmodel->_node2ResNode($literal);
		$this->assertIsA($result, 'ResLiteral');
		$this->assertEqual($result->getLanguage(),'DE');
		$this->assertEqual($result->getDatatype(),'type');
 		$model1->close();
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 }	
 	
 	
 	
 	
 	

 
 
 ?>