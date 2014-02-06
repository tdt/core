<?php
// ----------------------------------------------------------------------------------
// Class: testMm_setOperations_tests
// ----------------------------------------------------------------------------------

/**
 * Tests the set operations of the MemModel
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */


class testRDFSF_setOperations_tests extends UnitTestCase {
 	
	/**
	* tests if remove returns FALSE if trying to remove a statement which is not in MemModel
	* using all kinds of indices.
	*/
	function testRemoveFalse(){
 		$_SESSION['test']='MemModel remove false test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
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
 			$mod=$this->_generateModel(3,$i);
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
 			$mod=$this->_generateModel(3,$i);
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
 			$mod=$this->_generateModel(3,$i);
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
 			$mod=$this->_generateModel(3,$i);
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
		
		$model=new RDFSFModel();
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