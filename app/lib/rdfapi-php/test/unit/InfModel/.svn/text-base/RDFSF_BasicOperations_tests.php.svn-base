<?php

// ----------------------------------------------------------------------------------
// Class: testMm_basicOperations_tests
// ----------------------------------------------------------------------------------

/**
 * Tests the basic operations of the MemModel
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

class testRDFSF_basicOperations_tests extends UnitTestCase {
 	
	/**
	* tests if contains returns true if the given statement is in the MemModel
	* using all kinds of indices.
	*/
	function testContains(){
 		$_SESSION['test']='MemModel contains test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$stat=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
 			$this->assertTrue($mod->contains($stat));	
 		}
	}
	
	
	/**
	* tests if contains returns true if the given statement is in the MemModel
	* using all kinds of indices.
	*/
	function testContainsLiteral(){
 		$_SESSION['test']='MemModel contains literal test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelLiteral(3,$i);
 			$lit=new Literal('http://www.example.org/obj2');
 			$lit->setDatatype('test');
 			$stat=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),$lit);
 			$this->assertTrue($mod->contains($stat));	
 		}
	}
	
	
	
	/**
	* tests if contains returns false if the Literal in the given statement has wrong datatype
	* using all kinds of indices.
	*/
	function testContainsLiteralFalse(){
 		$_SESSION['test']='MemModel contains literal false test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelLiteral(3,$i);
 			$lit=new Literal('http://www.example.org/obj2');
 			$lit->setDatatype('notTest');
 			$stat=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),$lit);
 			$this->assertFalse($mod->contains($stat));	
 		}
	}
 	
 	/**
	* tests if contains returns false if the given statement is not in the MemModel
	* using all kinds of indices.
	*/
	function testContainsFalse(){
 		$_SESSION['test']='MemModel contains false test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$stat=new Statement(new Resource('http://www.example.org/subX'),new Resource('http://www.example.org/predX'),new Resource('http://www.example.org/objX'));
 			$this->assertFalse($mod->contains($stat));	
 		}
	}
 	
	/**
	* tests if containsAll returns true if all statements are in the given MemModel
	* using all kinds of indices.
	*/
	function testContainsAll(){
 		$_SESSION['test']='MemModel containsAll test';
 		$stat=new Statement(new Resource('http://www.example.org/sub1'),new Resource('http://www.example.org/pred1'),new Resource('http://www.example.org/obj1'));
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$mod2=$this->_generateModel(3,$i);
 			$mod->add($stat);
 			$this->assertTrue($mod->containsAll($mod2));	
 		}
	}
	
	
	
	/**
	* tests if containsAll returns false if not all statements are in the given MemModel
	* using all kinds of indices.
	*/
	function testContainsAllFalse(){
 		$_SESSION['test']='MemModel containsAll false test';
 		$stat=new Statement(new Resource('http://www.example.org/sub1'),new Resource('http://www.example.org/pred1'),new Resource('http://www.example.org/obj1'));
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$mod2=$this->_generateModel(3,$i);
 			$mod->remove($stat);
 			$this->assertFalse($mod->containsAll($mod2));	
 		}
	}
 	
	/**
	* tests if containsAny returns true any statements are in the given MemModel
	* using all kinds of indices.
	*/
	function testContainsAny(){
 		$_SESSION['test']='MemModel containsAny test';
 		$stat=new Statement(new Resource('http://www.example.org/sub1'),new Resource('http://www.example.org/pred1'),new Resource('http://www.example.org/obj1'));
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$mod2=$this->_generateModel(3,$i);
 			$mod->remove($stat);
 			$this->assertTrue($mod->containsAny($mod2));	
 		}
	}
 	
	/**
	* tests if containsAll returns false if no statement is in the given MemModel
	* using all kinds of indices.
	*/
	function testContainsAnyFalse(){
 		$_SESSION['test']='MemModel containsAll false test';
 		$stat=new Statement(new Resource('http://www.example.org/sub0'),new Resource('http://www.example.org/pred0'),new Resource('http://www.example.org/obj0'));
 		$stat2=new Statement(new Resource('http://www.example.org/sub1'),new Resource('http://www.example.org/pred1'),new Resource('http://www.example.org/obj1'));
 		$stat3=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$mod2=$this->_generateModel(3,$i);
 			$mod->remove($stat);
 			$mod->remove($stat2);
 			$mod->remove($stat3);
 			$this->assertFalse($mod->containsAll($mod2));	
 		}
	}
 	
	
 	
 	
//===================================================================
//                helper functions
//===================================================================
	
	/**
	* generates a test model containing given number of statements
	* and given indextype.
    *
    * @return Object MemModel $model
    * @param  int $num 
    * @param  int $des
    * @param  int $ind
    * @param  Object MemModel $needle
    */
    
	function _generateModel($stats,$ind){
		
		$model=new RDFSFModel();
		$model->index($ind);
		for($i=0;$i<$stats;$i++){
			$subs[$i]= new Resource('http://www.example.org/sub'.$i%3);
			$preds[$i]=new Resource('http://www.example.org/pred'.$i%5);
			$objs[$i]=new Resource('http://www.example.org/obj'.$i%9);
		}
		for($i=0;$i<$stats;$i++){
			$model->add(new Statement($subs[$i],$preds[$i],$objs[$i]));
		}
		
		return $model;
	}
	
	function _generateModelLiteral($stats,$ind){
		
		$model=new MemModel();
		$model->index($ind);
		for($i=0;$i<$stats;$i++){
			$subs[$i]= new Resource('http://www.example.org/sub'.$i%3);
			$preds[$i]=new Resource('http://www.example.org/pred'.$i%5);
			$objs[$i]=new Literal('http://www.example.org/obj'.$i%9);
			$objs[$i]->setDatatype('test');
		}
		for($i=0;$i<$stats;$i++){
			$model->add(new Statement($subs[$i],$preds[$i],$objs[$i]));
		}
		
		return $model;
	}
	
	
	

}



?>