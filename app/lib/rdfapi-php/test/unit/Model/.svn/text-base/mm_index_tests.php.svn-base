<?php
// ----------------------------------------------------------------------------------
// Class: mm_index_test
// ----------------------------------------------------------------------------------

/**
 * Tests the index functions of the MemModel
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

class testMm_index_test extends UnitTestCase {
 	
 	

 	// Tests if index is consistent after removing and adding Statements
 	function testAll_removeTest(){
 		$_SESSION['test']='All remove test';
 		$model1=new MemModel();
 		$rem=array(55,532,4,812,813,814,810,900,3,7,9,999,998,997,1,6);
 		$add=array(13,25,26,27,454,352,88,17,123);
 		for($i=0;$i<4;$i++){
 			$model1= $this->_generateModel(1000,1,$i,null);
 			foreach($rem as $key =>$value){
 				$model1->remove($model1->triples[$value]);
 			}
 			foreach($add as $key =>$value){
 				$model1->add($model1->triples[$value]);
 			}
 			
 			
 			if($i==0){
 				for($k=4;$k<7;$k++)
 				{
 					$this->assertTrue($this->_checkIndex($model1,$k));
 				}
 			}else{
 				$pass=$this->_checkIndex($model1,$i);
 				$this->assertTrue($pass);
 			}
 			
 			
 		}
 		
 	}
 	
 	
 	// Tests if index is consistent after removing Statements
 	function testEdge_removeTest(){
 		$_SESSION['test']='All remove test';
 		$model1=new MemModel();
 		$rem=array(0,1,2,3,11,12,13);
 		for($i=0;$i<4;$i++){
 			$model1= $this->_generateModel(14,1,$i,null);
 			foreach($rem as $key =>$value){
 				$model1->remove($model1->triples[$value]);
 			}
 			if($i==0){
 				for($k=4;$k<7;$k++)
 				{
 					$this->assertTrue($this->_checkIndex($model1,$k));
 				}
 			}else{
 				$pass=$this->_checkIndex($model1,$i);
 				$this->assertTrue($pass);
 			}
 			
 			
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
    
	function _generateModel($num,$des,$ind,$needle){
		
		$model=new MemModel();
		
		// generate Subjects
		for($i=0;$i<$num;$i++){
 			$subs[$i]=new Resource('http://www.example.org/Subject'.$i%6);
 		}
 		// generate Predicates
 		for($i=0;$i<$num;$i++){
 			$preds[$i]=new Resource('http://www.example.org/Predicate'.$i%7);
 		}
 		
 		// generate Objects
 		for($i=0;$i<$num;$i++){
 			$objs[$i]=new Resource('http://www.example.org/Object'.$i%5);
 		}
 		for($i=0;$i<$num;$i++){
 						$model->add(new Statement($subs[$i],$preds[$i],$objs[$i]));
 			}
	
		$model->index($ind);
		return $model;
	}
	
	
	/**
	* checks if given models index is consistent
    *
    * @return boolean $pass
    * @param  Object MemModel $model1 
    * @param  int $ind
    */
	
	function _checkIndex($model1,$ind){
		$pass=TRUE;
 		foreach($model1->indexArr[$ind] as $label => $posArr){
 			foreach($posArr as $num =>$posInd){
 				if(isset($model1->triples[$posInd])){
 					$stat=$model1->triples[$posInd];
 				}else{
 					return false;
 				}
 				if($stat==null){
 					return false;
 				}else{	
 					$s=$stat->getSubject();
 					$p=$stat->getPredicate();
 					$o=$stat->getObject();
 				}
 				switch($ind){
 					case 1:
 						if($stat== null){
 							return false;
 						}else{
 							$lab=$s->getLabel().$p->getLabel().$o->getLabel();
 						}
 					break;
 					
 					case 2:
 						if($stat== null){
 								return FALSE;
 							}else{
 								$lab=$s->getLabel().$p->getLabel();
 							}
 					break;
 					
 					case 3:
 						if($stat== null){
 								return FALSE;
 							}else{
 								$lab=$s->getLabel().$o->getLabel();
 							}
 					break;
 					
 					case 4:
 						if($stat== null){
 								return FALSE;
 							}else{
 								$lab=$s->getLabel();
 							}
 						break;
 					
 					case 5:
 						if($stat== null){
 								return FALSE;
 							}else{
 								$lab=$p->getLabel();
 							}		
 					break;
 					
 					case 6:
 						if($stat== null){
 								return FALSE;
 							}else{
 								$lab=$o->getLabel();	
 							}
 					break;
 				}
 				if($lab!=$label){
 					return FALSE;
 				}
 			}
 		}
		return $pass;
	}

}



?>