<?php
// ----------------------------------------------------------------------------------
// Class: ut_it_tests
// ----------------------------------------------------------------------------------

/**
 * Tests the basic operations of the iterator
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */


class testut_it_test extends UnitTestCase {
	
	function testHasNext(){
			$_SESSION['test']='HasNextTest';
			$mod=$this->_generateModel(10,-1);
			$it=$mod->getStatementIterator();
			for($i=0;$i<10;$i++){
				$this->assertTrue($it->HasNext());
				$it->next();
			}
			$this->assertFalse($it->HasNext());
			
		
	}
	
		function testHasNextRemove(){
			$_SESSION['test']='HasNextRemoveTest';
			$mod=$this->_generateModel(10,-1);
		
			$mod->remove($mod->triples[5]);
			$mod->remove($mod->triples[4]);
			$it=$mod->getStatementIterator();
			
			for($i=0;$i<8;$i++){
				$this->assertTrue($it->HasNext());
				$it->next();
				
			}
			$this->assertFalse($it->HasNext());	
		
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