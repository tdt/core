<?php

// ----------------------------------------------------------------------------------
// Class: rdfSerializer_test
// ----------------------------------------------------------------------------------

/**
 * Tests the RDF/XML Serializer
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

 class rdfSerializer_test extends UnitTestCase {

 	function testRdfSerializer(){
 		$_SESSION['test']='Rdf- Parser Empty Resource test';
 		$mod1= new MemModel();
 		$mod2= new MemModel();
 		$mod3=new MemModel();

 		$mod1->load('multipleTypes.rdf');
		$mod1->saveAs('mt1.rdf','rdf');

		$mod3->load('mt1.rdf');
		$mod2->load('multipleTypes.rdf');

		$_SESSION['mod1']=$mod3;
		$_SESSIOn['mod2']=$mod2;

 		if($mod3->equals($mod2)){
 			$pass=true;
 		}else{
 			$pass=false;
 		};
 		$this->assertTrue($pass);

 	}

 	
 }
?>