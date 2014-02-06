<?php

// ----------------------------------------------------------------------------------
// Class: NamedGraphs_tests.php
// ----------------------------------------------------------------------------------

/**
* Tests the namespacehandling
*
* @version  $Id$
* @author Tobias Gauß	<tobias.gauss@web.de>
*
* @package unittests
* @access	public
*/

class testNamedGraphs extends UnitTestCase {


	/**
	* tests functions on dataset level
	*/
	function testDatatset(){

		$_SESSION['test']='Dataset operations tests';
		$quad1= new Quad(new Resource('http://graph1'),new Resource('http://subject1'),new Resource('http://predicate1'),new Resource('http://object1'));
		$quad2= new Quad(new Resource('http://graph2'),new Resource('http://subject2'),new Resource('http://predicate2'),new Literal('http://object2'));
		$quad3= new Quad(new Resource('http://graph3'),new Resource('http://subject3'),new Resource('http://predicate3'),new BlankNode('http://object3'));

		$dataset = new DatasetMem('Dataset1');
		$dataset2 = new DatasetMem('Dataset2');

		$dataset2->addQuad($quad1);
		$dataset2->addQuad($quad2);
		$dataset->addQuad($quad3);


		$dataset->addAll($dataset2);

		$it = $dataset->findInNamedGraphs(null,null,null,null);

		$i = 0;
		while($it->valid()){
			$i++;
			$it->next();
		}
		$this->assertEqual(3,$i);
		$this->assertEqual(3,$dataset->countQuads());
		$this->assertTrue($dataset->containsQuad($quad1->getGraphName(),$quad1->getSubject(),$quad1->getPredicate(),$quad1->getObject()));
		$this->assertTrue($dataset->containsQuad($quad2->getGraphName(),$quad2->getSubject(),$quad2->getPredicate(),$quad2->getObject()));
		$this->assertTrue($dataset->containsQuad($quad3->getGraphName(),$quad3->getSubject(),$quad3->getPredicate(),$quad3->getObject()));


		$dataset->removeQuad($quad3);
		$this->assertEqual(2,$dataset->countQuads());
		$this->assertTrue($dataset->containsQuad($quad1->getGraphName(),$quad1->getSubject(),$quad1->getPredicate(),$quad1->getObject()));
		$this->assertTrue($dataset->containsQuad($quad2->getGraphName(),$quad2->getSubject(),$quad2->getPredicate(),$quad2->getObject()));
		$this->assertFalse($dataset->containsQuad($quad3->getGraphName(),$quad3->getSubject(),$quad3->getPredicate(),$quad3->getObject()));

	}





	/**
	* tests functions on graphset level
	*/
	function testGraphsetOperations(){
		$_SESSION['test']='Graphset operations tests';
		$graphset = ModelFactory::getDatasetMem('NGSet1');


		$graph1 = new NamedGraphMem('http://graph1');
		$graph2 =& $graphset->createGraph('http://graph2');

		$quad1= new Quad(new Resource('http://graph3'),new Resource('http://subject1'),new Resource('http://predicate1'),new Resource('http://object1'));
		$quad2= new Quad(new Resource('http://graph3'),new Resource('http://subject2'),new Resource('http://predicate2'),new Resource('http://object2'));
		$quad3= new Quad(new Resource('http://graph4'),new Resource('http://subject3'),new Resource('http://predicate3'),new Resource('http://object3'));
		$quad5= new Quad(new Resource('http://graph4'),new Resource('http://subject5'),new Resource('http://predicate5'),new Resource('http://object5'));

		$quad6= new Quad(new Resource('http://graph2'),new Resource('http://subject5'),new Resource('http://predicate5'),new Resource('http://object5'));
		$quad7= new Quad(new Resource('http://graph2'),new Resource('http://subject7'),new Resource('http://predicate5'),new Resource('http://object5'));


		$graphset->addQuad($quad1);
		$graphset->addQuad($quad3);
		$graphset->addQuad($quad2);
		$graphset->addQuad($quad6);
		$graphset->addQuad($quad7);
		$graphset->addQuad($quad5);

		$graphset->addNamedGraph($graph1);

		$this->assertTrue($graphset->containsNamedGraph('http://graph1'));



		$this->assertTrue($graphset->containsQuad($quad6->getGraphName(),$quad6->getSubject(),$quad6->getPredicate(),$quad6->getObject()));
		$this->assertTrue($graphset->containsQuad($quad7->getGraphName(),$quad7->getSubject(),$quad7->getPredicate(),$quad7->getObject()));


		$this->assertEqual(6,$graphset->countQuads());

		$graphset->removeNamedGraph('http://graph2');
		$this->assertFalse($graphset->containsNamedGraph('http://graph2'));

		$this->assertFalse($graphset->containsQuad($quad6->getGraphName(),$quad6->getSubject(),$quad6->getPredicate(),$quad6->getObject()));
		$this->assertFalse($graphset->containsQuad($quad7->getGraphName(),$quad7->getSubject(),$quad7->getPredicate(),$quad7->getObject()));

		$this->assertEqual(4,$graphset->countQuads());

		$this->assertTrue($graphset->containsQuad($quad5->getGraphName(),$quad5->getSubject(),$quad5->getPredicate(),$quad5->getObject()));
		$graphset->removeQuad($quad5);
		$this->assertEqual(3,$graphset->countQuads());
		$this->assertFalse($graphset->containsQuad($quad5->getGraphName(),$quad5->getSubject(),$quad5->getPredicate(),$quad5->getObject()));
	}

	/**
	* tests functions on graph level
	*/
	function testGraphOperations(){
		$_SESSION['test']='Graph operations tests';
		$graphset = ModelFactory::getDatasetMem('NGSet1');
		
	
		$graph1 = new NamedGraphMem('http://graph1');
		$graph2 = new NamedGraphMem('http://graph2');
		
		$st1= new Statement(new Resource('http://subject1'),new Resource('http://predicate1'),new Resource('http://object1'));
		$st2= new Statement(new Resource('http://subject2'),new Resource('http://predicate2'),new Literal('http://object2'));
		$st3= new Statement(new Resource('http://subject3'),new Resource('http://predicate3'),new BlankNode('http://object3'));

		$graph1->add($st1);
		$graph1->add($st2);
		$graph1->add($st3);
		
		$graph2->add($st1);
		$graph2->add($st2);
		$graph2->add($st3);
		
		$graphset->addNamedGraph($graph1);
		$graphset->addNamedGraph($graph2);
		
		$this->assertEqual(6,$graphset->countQuads());
		
		$this->assertTrue($graphset->containsQuad(new Resource('http://graph1'),$st1->getSubject(),$st1->getPredicate(),$st1->getObject()));
		$this->assertTrue($graphset->containsQuad(new Resource('http://graph1'),$st2->getSubject(),$st2->getPredicate(),$st2->getObject()));
		$this->assertTrue($graphset->containsQuad(new Resource('http://graph1'),$st3->getSubject(),$st3->getPredicate(),$st3->getObject()));
		
		$this->assertTrue($graphset->containsQuad(new Resource('http://graph2'),$st1->getSubject(),$st1->getPredicate(),$st1->getObject()));
		$this->assertTrue($graphset->containsQuad(new Resource('http://graph2'),$st2->getSubject(),$st2->getPredicate(),$st2->getObject()));
		$this->assertTrue($graphset->containsQuad(new Resource('http://graph2'),$st3->getSubject(),$st3->getPredicate(),$st3->getObject()));
		
		$this->assertEqual(6,$graphset->countQuads());
		$this->assertEqual(6,$graphset->countQuads());
		
		
	}
	
	/**
	* tests reference functions
	*/
	function testRefOperations(){
		
		$_SESSION['test']='Reference operations tests';
		$graphset = ModelFactory::getDatasetMem('NGSet1');
		
	
		$graph1 = new NamedGraphMem('http://graph1');
		$graph2 = new NamedGraphMem('http://graph2');
		
		$st1= new Statement(new Resource('http://subject1'),new Resource('http://predicate1'),new Resource('http://object1'));
		$st2= new Statement(new Resource('http://subject2'),new Resource('http://predicate2'),new Literal('http://object2'));
		$st3= new Statement(new Resource('http://subject3'),new Resource('http://predicate3'),new BlankNode('http://object3'));

		$graph1->add($st1);
		$graph1->add($st2);
		$graph1->add($st3);
		
		$graph2->add($st1);
		$graph2->add($st2);
		$graph2->add($st3);
		
		$graphset->addNamedGraph($graph1);
		$graphset->addNamedGraph($graph2);
		
		$grref = &$graphset->getNamedGraph('http://graph1');
		$this->assertEqual(6,$graphset->countQuads());
		$grref->remove($st1);
		$this->assertEqual(5,$graphset->countQuads());
		$grref->add($st1);
		$this->assertEqual(6,$graphset->countQuads());
	}
	
	
	
	/**
	* tests operations on defaultgraph
	*/
	function testDefOperations(){
		
		$_SESSION['test']='Default graph operations tests';
		$graphset = ModelFactory::getDatasetMem('NGSet1');
		$st1= new Statement(new Resource('http://subject1'),new Resource('http://predicate1'),new Resource('http://object1'));
		$st2= new Statement(new Resource('http://subject2'),new Resource('http://predicate2'),new Literal('http://object2'));
		
		
		$this->assertTrue($graphset->hasDefaultGraph());
		$defaultGraph = &$graphset->getDefaultGraph();
		$this->assertEqual($defaultGraph->size(),0);
		$defaultGraph->add($st1);
		

		$graph2 = new NamedGraphMem('http://graph2');
		$graph2->add($st2);
		$graphset->setDefaultGraph($graph2);
		
		$defaultGraph2 = &$graphset->getDefaultGraph();
		
		$this->assertFalse($defaultGraph2->contains($st1));
		$this->assertTrue($defaultGraph2->contains($st2));
		$this->assertEqual($defaultGraph2->size(),1);
	
	}


}






?>