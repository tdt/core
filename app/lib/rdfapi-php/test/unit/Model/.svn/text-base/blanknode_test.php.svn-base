<?php

// ----------------------------------------------------------------------------------
// Class: blanknode_test
// ----------------------------------------------------------------------------------

/**
 * This class tests the functions of a Blanknode
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */


class blanknode_test extends UnitTestCase {
	 	
	function testGetID(){
		$_SESSION['test']='bnode getID test';
		$bnode=$this->_generateBnode();
		$this->assertEqual($bnode->getId(),'http://www.example.orglocalname');
	}
	
	function testGetLabel(){
		$_SESSION['test']='bnode getLabel test';
		$bnode=$this->_generateBnode();
		$this->assertEqual($bnode->getLabel(),'http://www.example.orglocalname');
	}
	
	function testToString(){
		$_SESSION['test']='bnode toString test';
		$bnode=$this->_generateBnode();
		$this->assertEqual($bnode->toString(),'bNode("http://www.example.orglocalname")');
	}
	
	function testEquals(){
		$_SESSION['test']='bnode Euqals test';
		$bnode1=$this->_generateBnode();
		$bnode2=$this->_generateBnode();
		$this->assertTrue($bnode1->equals($bnode2));
	}
	
	function testNotEquals(){
		$_SESSION['test']='bnode NotEuqals test';
		$bnode1=$this->_generateBnode();
		$bnode2=new BlankNode('http://www.example.orglocalname','localname2');
		$bnode3=new BlankNode('http://www.example.orglocalname1','localname');
		$this->assertFalse($bnode1->equals($bnode2));
		$this->assertFalse($bnode1->equals($bnode3));
		
	}
	

	/**
	*  generate a Blanknode
	*
	*/
	function _generateBnode(){
		$node=new BlankNode('http://www.example.org','localname');
		return $node;
	}



}
 	
?>