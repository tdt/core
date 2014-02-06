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

class testMm_basicOperations_tests extends UnitTestCase {
 	
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
	
	
	
	/**
	* tests if remove returns FALSE if trying to remove a statement which is not in MemModel
	* using all kinds of indices.
	*/
	function testRemoveFalse(){
 		$_SESSION['test']='MemModel remove false test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelt(3,$i);
 			$stat=new Statement(new Resource('http://www.example.org/a'),new Resource('http://www.example.org/b'),new Resource('http://www.example.org/c'));
 			$this->assertFalse($mod->remove($stat));	
 		}
	}
 	
 	/**
	* tests if a statement is successfully removed from a MemModel
	* using all kinds of indices.
	*/
 	 function testRemove(){
 		$_SESSION['test']='MemModel remove test';
		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelt(3,$i);
 			$stat=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
 			$this->assertTrue($mod->remove($stat));	
 			$this->assertFalse($mod->contains($stat));
		}
	}
 	
	/**
	* tests if a statement is successfully added to a MemModel
	* using all kinds of indices
	*/
 	function testAdd(){
 		$_SESSION['test']='MemModel add test';
		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelt(3,$i);
 			$stat=new Statement(new Resource('http://www.example.org/sub4'),new Resource('http://www.example.org/pred4'),new Resource('http://www.example.org/obj4'));
 			$this->assertFalse($mod->contains($stat));
 			$mod->add($stat);	
 			$this->assertTrue($mod->contains($stat));
		}
 	}
 	
 	/**
	* tests if addWithoutDuplicates returns false if adding a
	* statement which is already in the MemModel
	*
	*/
 	function testAddWithoutDuplicates(){	
 	 	$_SESSION['test']='MemModel addWithoutDuplicates test';
		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelt(3,$i);
 			$stat=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
 			$this->assertTrue($mod->contains($stat));
 			$this->assertFalse($mod->addWithoutDuplicates($stat));	
 			$this->assertTrue($mod->contains($stat));
 			$this->assertEqual($mod->size(),3);
		}
 	}
 	
 	
 	/**
	* tests if addWithoutDuplicates returns false if adding a
	* statement which is already in the MemModel
	*
	*/
 	function testAddWithoutDuplicatesNegative(){	
 	 	$_SESSION['test']='MemModel addWithoutDuplicatesNegative test';
		for($i=-1;$i<4;$i++){
			unset($mod);
 			$mod=$this->_generateModelt(3,$i);
 			$stat=new Statement(new Resource('http://www.example.org/subx'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
 			$this->assertFalse($mod->contains($stat));
 			$this->assertTrue($mod->addWithoutDuplicates($stat));	
 			$this->assertTrue($mod->contains($stat));
 			$this->assertEqual($mod->size(),4);
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
		
		$model=new MemModel();
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
    
	function _generateModelt($stats,$ind){
		
		$model=new MemModel();
		$model->index($ind);
		for($i=0;$i<$stats;$i++){
			$subs[$i]= new Resource('http://www.example.org/sub'.$i);
			$preds[$i]=new Resource('http://www.example.org/pred'.$i);
			$objs[$i]=new Resource('http://www.example.org/obj'.$i);
		}
		for($i=0;$i<$stats;$i++){
			$model->add(new Statement($subs[$i],$preds[$i],$objs[$i]));
		}
		
		return $model;
	}
	
	
	
	

}



?>