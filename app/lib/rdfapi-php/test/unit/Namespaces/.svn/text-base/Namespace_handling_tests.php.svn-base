<?php

// ----------------------------------------------------------------------------------
// Class: Namespace_handling_tests.php
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

class testNamespaceHandling extends UnitTestCase {

	/**
	* tests if the default_prefixes array is used.
	*/
	function testUsingDefault(){
		$_SESSION['test']='Using default array test';
		$model = new MemModel();
		$pars = new N3Parser();
		$model = $pars->parse2model($this->_generateModelString());
		// delete default prefixes
		global $default_prefixes;
		$backup = $default_prefixes;
		foreach ($default_prefixes as $name => $pref){
			unset ($default_prefixes[$name]);
		}
		$default_prefixes=array('xxx' => RDF_NAMESPACE_URI);
		// serialize model;
		$ser = new RdfSerializer();
		$save = $ser->serialize($model);
		$model1 = new MemModel();
		$model1->load($save);

		$this->assertEqual($model1->parsedNamespaces[RDF_NAMESPACE_URI],'xxx');
		$model1->removeNamespace(RDF_NAMESPACE_URI);
		//$this->assertEqual($model1->parsedNamespaces[RDF_NAMESPACE_URI] , null);
		$default_prefixes = $backup;
	}





	/**
	* tests if the manual set namespaceprefix 'foo' overwrites the prefix 'rdf' defined in
	* the default_prefixes array.
	*/
	function testOverwritingDefaultManual(){
		$_SESSION['test']='Overwriting default manual test';
		$model = new MemModel();
		$pars = new N3Parser();
		$model = $pars->parse2model($this->_generateModelString());
		$ser = new RdfSerializer();
		$string = $ser->serialize($model);
		$model2 = new MemModel();
		$model2->load($string);
		$this->assertEqual($model2->parsedNamespaces[RDF_NAMESPACE_URI],'rdf');
		$model2->addNamespace('foo',RDF_NAMESPACE_URI);
		$this->assertEqual($model2->parsedNamespaces[RDF_NAMESPACE_URI],'foo');
		$model2->removeNamespace(RDF_NAMESPACE_URI);
		//$this->assertEqual($model2->parsedNamespaces[RDF_NAMESPACE_URI] , null);
	}



	/**
	* parser overwrites the prefixes defined in the default_prefixes array
	*/
	function testOverwritingDefaultParser(){
		$_SESSION['test']='Overwriting default parser test';
		$string="<?xml version='1.0'?>
 				<rdf:RDF xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'
             			xmlns:exterms='http://www.example.org/terms/'>
					<rdf:Description rdf:about='http://www.example.org/index.html'>
						<exterms:creation-date>August 16, 1999</exterms:creation-date>
					</rdf:Description>
				</rdf:RDF>";
		// delete default prefixes
		global $default_prefixes;
		$backup = $default_prefixes;
		foreach ($default_prefixes as $name => $pref){
			unset ($default_prefixes[$name]);
		}
		$default_prefixes=array('foo' => RDF_NAMESPACE_URI);
		$model = new MemModel();
		$model->load($string);
		$nmsp = $model->getParsedNamespaces();
		$this->assertEqual($nmsp[RDF_NAMESPACE_URI],'rdf');
		$default_prefixes = $backup;
	}



	//===================================================================
	//                helper functions
	//===================================================================

	function _generateModelString(){
		$string="
		<urn:animals:data> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq> .
		<urn:animals:data> <http://www.w3.org/1999/02/22-rdf-syntax-ns#_1> <urn:animals:lion> .
		<urn:animals:hippopotamus> <http://www.some-ficticious-zoo.com/rdf#class> 'Mammal' .";


		return $string;

	}




}






?>