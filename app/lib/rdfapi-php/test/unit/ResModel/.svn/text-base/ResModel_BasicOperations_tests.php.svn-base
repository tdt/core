<?php

// ----------------------------------------------------------------------------------
// Class: ResModel_basicOperations_tests
// ----------------------------------------------------------------------------------

/**
 * Tests the basic operations of the ResModel
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

class testResModel_basicOperations_tests extends UnitTestCase {
 	
	/**
	* tests if contains returns true if the given ResStatement belongs to
	* the model.
	*/
	function testContains(){
 		$_SESSION['test']='ResModel contains test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$resmodel=new ResModel($mod);		
 			$statTest=new Statement(new ResResource('http://www.example.org/sub2'),new ResResource('http://www.example.org/pred2'),new ResResource('http://www.example.org/obj2'));
 			$this->assertTrue($resmodel->contains($statTest));	
 		}
	}
	
	/**
	* tests if contains returns true if the given statement belongs to the model
	* using all kinds of indices.
	*/
	function testContainsLiteral(){
 		$_SESSION['test']='ResModel contains literal test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelLiteral(3,$i);
 			$lit=new ResLiteral('http://www.example.org/obj2','DE');
			$resmodel=new ResModel($mod);
 			$statTest=new Statement(new ResResource('http://www.example.org/sub2'),new ResResource('http://www.example.org/pred2'),$lit);
 			$this->assertTrue($resmodel->contains($statTest));	
 		}
	}
	
	
	/**
	* tests if contains returns false if the Literal in the given statement has wrong datatype
	* using all kinds of indices.
	*/
	function testContainsLiteralFalse(){
 		$_SESSION['test']='ResModel contains literal false test/ wrong language';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModelLiteral(3,$i);
 			$lit=new ResLiteral('http://www.example.org/obj2','EN');
			$resmodel=new ResModel($mod);
 			$statTest=new Statement(new ResResource('http://www.example.org/sub2'),new ResResource('http://www.example.org/pred2'),$lit);
 			$this->assertFalse($resmodel->contains($statTest));	
 		}
	}
 	
 	/**
	* tests if contains returns false if the given statement is not in the MemModel
	* using all kinds of indices.
	*/
	function testContainsFalse(){
 		$_SESSION['test']='ResModel contains false test';
 		for($i=-1;$i<4;$i++){
 			$mod=$this->_generateModel(3,$i);
 			$resmodel=new ResModel($mod);		
 			$statTest=new Statement(new ResResource('http://www.example.org/subX'),new ResResource('http://www.example.org/pred2'),new ResResource('http://www.example.org/obj2'));
 			$this->assertFalse($resmodel->contains($statTest));	
 		}
	}

    function testContainerIsSeq() {
        $model = ModelFactory::getResModel(MEMMODEL);
        $seq = $model->createSeq('http://example.org/my_seq');
        $this->assertTrue($seq->isSeq());
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
		$model=new InfModelB();
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
		
		$model=new InfModelB();
		$model->index($ind);
		for($i=0;$i<$stats;$i++){
			$subs[$i]= new Resource('http://www.example.org/sub'.$i%3);
			$preds[$i]=new Resource('http://www.example.org/pred'.$i%5);
			$objs[$i]=new Literal('http://www.example.org/obj'.$i%9,'DE');
			
		}
		for($i=0;$i<$stats;$i++){
			$model->add(new Statement($subs[$i],$preds[$i],$objs[$i]));
		}
		
		return $model;
	}
	
	
	

}



?>
