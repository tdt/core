<?php

// ----------------------------------------------------------------------------------
// Class: ut_FindIterator_tests
// ----------------------------------------------------------------------------------

/**
 * Tests the basic operations of the FindIterator
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */


class testut_FindIt_test extends UnitTestCase {
	
	function testHasNext(){
		for($i=-1;$i<4;$i++){
			$_SESSION['test']='HasNextTest';
			$mod=$this->_generateModel(10,$i);
			$it=$mod->findAsIterator(null,null,null);
			for($k=0;$k<10;$k++){
				$this->assertTrue($it->hasNext());
				$it->Next();
				$current=$it->current();
				$should=$mod->triples[$k];
				$this->assertEqual($current->toString(),$should->toString());
			}
			$this->assertFalse($it->hasNext());
		}
	}
	
	function testHasNextS(){
		for($i=-1;$i<4;$i++){
			$_SESSION['test']='HasNextSTest';
			$mod=$this->_generateModel(10,$i);
			$it=$mod->findAsIterator(new Resource("http://www.example.org/sub0"),null,null);
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertFalse($it->hasNext());
		}
	}
	
	
	function testHasNextRemove(){
		for($i=-1;$i<4;$i++){
			$_SESSION['test']='HasNextRemoveTest';
			$mod=$this->_generateModel(10,$i);
			$it=$mod->findAsIterator(new Resource('http://www.example.org/sub0'),null,null);
			$mod->remove($mod->triples[0]);
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertFalse($it->hasNext());

		}
	}
	
	function testHasNextSPO(){
		for($i=-1;$i<4;$i++){
			$_SESSION['test']='HasNextSPOTest';
			$mod=$this->_generateModel(10,$i);
			$it=$mod->findAsIterator(new Resource("http://www.example.org/sub0"),new Resource("http://www.example.org/pred0"),new Resource("http://www.example.org/obj0"));
			$this->assertTrue($it->hasNext());
			$it->Next();
			$this->assertFalse($it->hasNext());
		}
	}
	
	function testHasNextSPOremove(){
		for($i=-1;$i<4;$i++){
			$_SESSION['test']='HasNextSPOTest remove';
			$mod=$this->_generateModel(10,$i);
			$mod->remove($mod->triples[0]);
			$it=$mod->findAsIterator(new Resource("http://www.example.org/sub0"),new Resource("http://www.example.org/pred0"),new Resource("http://www.example.org/obj0"));
			$this->assertFalse($it->hasNext());
			$this->assertEqual($it->current(),null);
		}
	}
	
	


	
	
	
	
//===================================================================
//                helper functions
//===================================================================

	function _generateModel($num,$ind){
		$model=new MemModel();
		$model->index($ind);
		for($i=0;$i<$num;$i++){
			$subs[$i]=new Resource('http://www.example.org/sub'.$i%3);
			$preds[$i]=new Resource('http://www.example.org/pred'.$i%5);
			$objs[$i]=new Resource('http://www.example.org/obj'.$i%9);
			$stat=new Statement($subs[$i],$preds[$i],$objs[$i]);
			$model->add($stat);
			
		}
		return $model;				
	}
 	
 	
}

?>