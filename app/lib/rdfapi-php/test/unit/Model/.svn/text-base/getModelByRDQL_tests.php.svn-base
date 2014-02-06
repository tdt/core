<?php
// ----------------------------------------------------------------------------------
// Class: getModelByRDQL_tests
// ----------------------------------------------------------------------------------

/**
* This class tests the getModelByRDQL() functions of the Model
*
* @version  $Id$
* @author Tobias Gauß	<tobias.gauss@web.de>
*
* @package unittests
* @access	public
*/
class getModelByRDQL_tests extends UnitTestCase {

	function testGetModelByRDQL(){
		$_SESSION['test']='simple rdql query';
		$model=new MemModel();
		$model->load('employees.rdf');
		$query=' SELECT ?fullName
				WHERE (?x, vcard:FN, ?fullName)
				USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>';
		$model2 = $model->getMemModelByRDQL($query);
		$this->assertEqual($model2->size(),3);
	}

	function testGetModelByRDQL2(){
		if(isset($model2));
			unset($model2);
		
		$_SESSION['test']='simple rdql query2';
		$model=new MemModel();
		$model->load('employees.rdf');
		$query=' SELECT *
				WHERE (?x, vcard:Given, "Monica")
				USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>';
		$model2 = $model->getMemModelByRDQL($query);
		$this->assertEqual($model2->size(),1);
	}

	function testGetModelByRDQL3(){
		if(isset($model2));
			unset($model2);
		
		$_SESSION['test']='simple rdql query3';
		$model=new MemModel();
		$model->load('employees.rdf');
		$query=' SELECT ?x
				WHERE (?x,<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>, <http://www.w3.org/2001/vcard-rdf/3.0#work>)';
		$model2 = $model->getMemModelByRDQL($query);
		$this->assertEqual($model2->size(),6);
		$model3 = $model2->find(null,RDF::TYPE(),new Resource('http://www.w3.org/2001/vcard-rdf/3.0#work'));
		$this->assertEqual(6,$model3->size());
	}


	function testGetModelByRDQL4(){
		if(isset($model2));
			unset($model2);
		
		$_SESSION['test']='simple rdql query4';
		$model=new MemModel();
		$model->load('employees.rdf');
		$query='  SELECT ?givenName, ?age
// This is an example of an one-line comment
WHERE (?x, vcard:N, ?blank),
      (?blank, vcard:Given, ?givenName),
      (?x, /* and this is another type of comments */ v:age, ?age)
AND ?age > 30
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#> v FOR <http://sampleVocabulary.org/1.3/People#>';
		$model2 = $model->getMemModelByRDQL($query);
		$statement1 = new Statement(new BlankNode($model2),VCARD::GIVEN(),new Literal('Bill'));
		$statement2 = new Statement(new BlankNode($model2),VCARD::GIVEN(),new Literal('George'));
		$this->assertEqual($model2->size(),6);
	}

	function testGetModelByRDQL5(){
		if(isset($model2));
			unset($model2);
		
		$_SESSION['test']='simple rdql query5';
		$model=new MemModel();
		$model->load('employees.rdf');
		$query='  SELECT ?givenName ?age ?telNumberHome
WHERE (?person vcard:N ?blank1)
      (?blank1 vcard:Given ?givenName)
      (?person v:age ?age)
      (?person vcard:TEL ?blank2)
      (?blank2 rdf:value ?telNumberHome)
      (?blank2 rdf:type vcard:home)
      (?person vcard:TEL ?blank3)
      (?blank3 rdf:value ?telNumberOffice)
      (?blank3 rdf:type vcard:work)
AND ?telNumberOffice eq "+1 111 2222 668"
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>
      v FOR <http://sampleVocabulary.org/1.3/People#>';
		$model2 = $model->getMemModelByRDQL($query);
		$this->assertEqual($model2->size(),9);
	}


	function testGetModelByRDQL6(){
		if(isset($model2));
			unset($model2);
		
		$_SESSION['test']='simple rdql query6';
		$model=new MemModel();
		$model->load('employees.rdf');
		$query='  SELECT ?resource, ?email
WHERE (?resource, vcard:N, ?blank1)
      (?blank1, vcard:Family, ?familyName)
      (?resource, vcard:EMAIL, ?blank2)
      (?blank2, rdf:value, ?email)
      (?blank2, rdf:type, vcard:work)
AND ?familyName ~~ "/^M/"
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>';
		$model2 = $model->getMemModelByRDQL($query);
		$this->assertEqual($model2->size(),5);

	}






}



?>