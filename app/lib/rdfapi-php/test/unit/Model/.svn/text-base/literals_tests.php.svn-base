<?php
// ----------------------------------------------------------------------------------
// Class: Literals_tests 
// ----------------------------------------------------------------------------------

/**
 * This class tests the functions of a Literal
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

class Literals_tests extends UnitTestCase {

	/**
	* tests if equals() works according to the RDF specifications
	*
	*/
	function testEquals(){
		$_SESSION['test']='Litras Equals test';
		$literal1=new Literal('test');
		$literal2=new Literal('test');
		$this->assertTrue($literal1->equals($literal2));
		$literal2->setLanguage('DE');
		$this->assertFalse($literal1->equals($literal2));
		$literal1->setLanguage('FR');
		$this->assertFalse($literal1->equals($literal2));
		$literal1->setLanguage('DE');
		$this->assertTrue($literal1->equals($literal2));
		$literal1->setDatatype("http://www.w3.org/TR/xmlschema-2/integer");
		$this->assertFalse($literal1->equals($literal2));
		$literal2->setDatatype("http://www.w3.org/TR/xmlschema-2/integer1");
		$this->assertFalse($literal1->equals($literal2));
		$literal2->setDatatype("http://www.w3.org/TR/xmlschema-2/integer");
		$this->assertTrue($literal1->equals($literal2));
		
	}

}
 	
?>