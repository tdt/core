<?php

// ----------------------------------------------------------------------------------
// Class: testOntModel_basicOperations_tests
// ----------------------------------------------------------------------------------

/**
 * Tests the basic operations of the OntModel
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

class testOntModel_basicOperations_tests extends UnitTestCase {
	
	function testOntmodel_Tests(){
		$_SESSION['test']='Ont Model test';
		
		$res = ModelFactory::getOntModel(INFMODELB,RDFS_VOCABULARY,'http://www.example.orgURI');
		$ontClass1 = $res->createOntClass('class1');
		$literal1 = $res->createLiteral('ein Label','de');
		$literal2 = $res->createLiteral('a comment','en');
		$resource1 = $res->createResource('http:\\www.example.orgDefinedBy');
		$resource2 = $res->createResource('http:\\www.example.orgRDFType');
		$resource3 = $res->createResource('http:\\www.example.orgSeeAlso');	
		$instance1 = $ontClass1->createInstance('testInstance');		
		$instance1->addLabelProperty($literal1);
		$instance1->addComment($literal2);
		$instance1->addIsDefinedBy($resource1);
		$instance1->addRDFType($resource2);
		$instance1->addSeeAlso($resource3);
				
		$this->assertEqual($instance1->getLabelProperty(),$literal1);
		$this->assertEqual($instance1->getComment(),$literal2);
		$this->assertEqual($instance1->getIsDefinedBy(),$resource1);
		$list = $instance1->listRDFTypes();
		$this->assertEqual(2,count($list));
		$this->assertEqual($instance1->getSeeAlso(),$resource3);
		
		$ontClass2 = $res->createOntClass('class2');
		$ontClass1->addSubClass($ontClass2);
		$this->assertEqual($res->size(),8);
		
		$instance2 = $ontClass2->createInstance('testInstance2');
	
		$literal3=new ResLiteral('other comment');
		$instance2->addComment($literal3);
		$this->assertEqual($res->size(),11);
		
		$ontClass3 = $res->createOntClass('class3');
		$ontClass1->addSuperClass($ontClass3);
		$this->assertEqual($res->size(),15);
		$this->assertEqual(count($instance1->listRDFTypes()),3);
		$this->assertEqual(count($instance2->listRDFTypes()),3);
		
		$instance1->removeRDFType($resource2);
		$this->assertEqual(count($instance1->listRDFTypes()),2);
		$this->assertEqual(count($instance2->listRDFTypes()),3);
		
		$individual1 = $res->createIndividual('http:\\individual1');
		$individual1->addLabelProperty($literal1);
		$this->assertEqual($res->size(),15);
		
		$instance3 = $ontClass2->createInstance('instance3');
		$instance3->addLabelProperty($literal1);
		$this->assertEqual(19,$res->size());
		
		$iter = $ontClass2->listInstances();
		$i=0;
		for($iter->rewind();$iter->valid();$iter->next()){
			$i++;
		}		
		$this->assertEqual(2,$i);

	}
	


}

?>