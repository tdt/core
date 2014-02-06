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

class testRDFSJenaInfmodelTests extends UnitTestCase {
 	
	/**
	* Loop trough the jena infmodel tests
	*/
	function testRdfsFModel()
	{
		$testURI='http://www.hpl.hp.com/semweb/2003/query_tester#';
		$testmodel=new MemModel($testURI);
		$testmodel->load(RDFS_INF_TESTFILES.'rdfs/manifest-standard.rdf');
		$i=1;
		do 
		{
			$inf= new RDFSFModel();		
			$res1=$testmodel->find(new Resource(RDFS_INF_TESTFILES.'rdfs/test'.$i++),null,null);
			if ($res1->isEmpty())
				break;
					
			$findTBOX=$res1->find(null,new Resource($testURI.'tbox'),null);
			$inf->load(RDFS_INF_TESTFILES.$findTBOX->triples[0]->getLabelObject());
	
			$findDATA=$res1->find(null,new Resource($testURI.'data'),null);
			$inf->load(RDFS_INF_TESTFILES.$findDATA->triples[0]->getLabelObject());
						
			$findQUERY=$res1->find(null,new Resource($testURI.'query'),null);
			$query =$this->_doFindFromFile(RDFS_INF_TESTFILES.$findQUERY->triples[0]->getLabelObject(),$inf);
			
			$result = new MemModel();
			$findRESULT=$res1->find(null,new Resource($testURI.'result'),null);
			$result->load(RDFS_INF_TESTFILES.$findRESULT->triples[0]->getLabelObject());

			$isEqual=$result->equals($query);
			#$isEqual=$query->containsAll($result);
			
			#if (!$isEqual)
			{
				#$query->writeAsHtmlTable();
				#$result->writeAsHtmlTable();
				#$subtract=$query->subtract($result);
				#$subtract->writeAsHtmlTable();
				
			};
			
			$findDATA=$res1->find(null,new Resource($testURI.'description'),null);
			echo '<b>'.$findDATA->triples[0]->getLabelObject().' (RDFSFModel)</b><BR>';
			
			$this->assertTrue($isEqual);
	
		} while (true);	
	}


	function _doFindFromFile($file,& $model)
	{
		$nullVarURIs=array('var:x','var:y','var:z');
		$mod= new MemModel();
		$mod->load($file);
		
		$return=new MemModel();
		
		foreach ($mod->triples as $statement)
		{
		
		
			if (in_array($statement->getLabelSubject(),$nullVarURIs))
			{
				$findS=null;
			} else 
			{
				$findS=	$statement->getSubject();
			};
			
			if (in_array($statement->getLabelPredicate(),$nullVarURIs))
			{
				$findP=null;
			} else 
			{
				$findP=	$statement->getPredicate();
			};
			
			if (in_array($statement->getLabelObject(),$nullVarURIs))
			{
				$findO=null;
			} else 
			{
				$findO=	$statement->getObject();
			};
			$return->addModel($model->find($findS,$findP,$findO));
		};

		return $return;	
	}
	
}
?>