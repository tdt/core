<?php
// ----------------------------------------------------------------------------------
// Class: testMm_search_test
// ----------------------------------------------------------------------------------

/**
 * Tests the find functions of the MemModel
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

class testMm_search_test extends UnitTestCase {
 	
 	
	/**
	* tests if find() finds a complete statement in MemModel
	*/
 	function testFindTest(){
 		$_SESSION['test']='search test';
 		$model1=new MemModel();
 		$needle=new Statement(new Resource('http://www.example.org/needle'),new Resource('http://www.example.org/pred'),new Resource('http://www.example.org/ob'));

 		for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
			$this->assertNotEqual(null,$model1->find($needle->getSubject(),$needle->getPredicate(),$needle->getObject())); 		
 		}
 		
 	    $model1->close();
 	}
 	
 	/**
	* tests if find() finds a statement with a given subject in MemModel
	*/
 	function testFindSubTest(){
 		$_SESSION['test']='search Subject test';
 		$model1=new MemModel();
 		$needle=new Statement(new Resource('http://www.example.org/Subject6'),new Resource('http://www.example.org/pred'),new Resource('http://www.example.org/ob'));

 		for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
			$this->assertNotEqual(null,$model1->find($needle->getSubject(),null,null)); 		
 		}
 		
 		for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
			$this->assertNotEqual(null,$model1->find(null,$needle->getPredicate(),null)); 		
 		}
 		
 		 for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
			$this->assertNotEqual(null,$model1->find(null,null,$needle->getObject())); 		
 		}
 		
 		
 	    $model1->close();
 	}
 	
 	
 	
 	
 	/**
	* tests if find() finds a statement in MemModel
	*/
 	function testFindTestEq(){
 		$_SESSION['test']='search Eq test';
 		$model1=new MemModel(); 		
 		$needle=new Statement(new Resource('http://www.example.org/Subject3'),new Resource('http://www.example.org/pred'),new Resource('http://www.example.org/ob'));

 		for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
 			$res=$model1->find($needle->getSubject(),null,null);
			$this->assertNotEqual(0,$res->size()); 			
 		}
 		
 	    $model1->close();
 	}

 	/**
	* tests if find() finds a statement in MemModel
	*/
 	function testNotFindTest(){
 		$_SESSION['test']='no match test';
 		$model1=new MemModel(); 		
 		$needle=new Statement(new Resource('http://www.example.org/Subject3'),new Resource('http://www.example.org/pred'),new Resource('http://www.example.org/ob'));
 		$stat=new Statement(new Resource('http://www.example.org./subX'),new Resource('http://www.example.org./predX'),new Resource('http://www.example.org./objX'));
 		for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
 			$res=$model1->find($stat->getSubject(),null,null);
			$this->assertEqual(0,$res->size()); 			
 		}
 		
 	    $model1->close();
 	}

 	
 	/**
	* tests if findFirstMatchingStatement() finds a statement in MemModel
	*/
 	function testFindFirstTest(){
 		$_SESSION['test']='find first match';
 		$model1=new MemModel(); 		
 		$needle=new Statement(new Resource('http://www.example.org/Subject3'),new Resource('http://www.example.org/pred'),new Resource('http://www.example.org/ob'));
 		$stat=new Statement(new Resource('http://www.example.org/Subject0'),new Resource('http://www.example.org/Predicate0'),new Resource('http://www.example.org/Object0'));
 		for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
 			$res=$model1->findFirstMatchingStatement($stat->getSubject(),null,null);
			$result=$res->getSubject();
			$res2=$stat->getSubject();
 			$this->assertEqual($result->getLabel(),$res2->getLabel());			
 		}
 	    $model1->close();
 	}
 	
 	 function testFindFirstNullTest(){
 		$_SESSION['test']='find first match null';
 		$model1=new MemModel(); 		
 		$needle=new Statement(new Resource('http://www.example.org/Subject3'),new Resource('http://www.example.org/pred'),new Resource('http://www.example.org/ob'));
 		for($i=-1;$i<4;$i++){
 			$model1= $this->_generateModel(100,2,$i,$needle);
 			$res=$model1->findFirstMatchingStatement(null,null,null);	
			$should=$model1->triples[0];
			$this->assertEqual($res->toString(),$should->toString());			
 		}
 	    $model1->close();
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
 						if($i==50){
 							$model->add($needle);
 						}
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
 		$count=0;
 		foreach($model1->indexArr[$ind] as $label => $posArr){
 			foreach($posArr as $num =>$posInd){
 				if(isset($model1->triples[$posInd])){
 					$stat=$model1->triples[$posInd];
 				}else{
 					$pass=false;
 				}
 				if($stat==null){
 					$pass= false;
 				}else{	
 					$s=$stat->getSubject();
 					$p=$stat->getPredicate();
 					$o=$stat->getObject();
 				}
 				switch($ind){
 					case 1:
 						if($stat== null){
 							$pass= FALSE;
 						}else{
 							$lab=$s->getLabel().$p->getLabel().$o->getLabel();
 						}
 					break;
 					
 					case 2:
 						if($stat== null){
 								$pass= FALSE;
 							}else{
 								$lab=$s->getLabel().$p->getLabel();
 							}
 					break;
 					
 					case 3:
 						if($stat== null){
 								$pass= FALSE;
 							}else{
 								$lab=$s->getLabel().$o->getLabel();
 							}
 					break;
 					
 					case 4:
 						if($stat== null){
 								$pass= FALSE;
 							}else{
 								$lab=$s->getLabel();
 							}
 						break;
 					
 					case 5:
 						if($stat== null){
 								$pass= FALSE;
 							}else{
 								$lab=$p->getLabel();
 							}		
 					break;
 					
 					case 6:
 						if($stat== null){
 								$pass= FALSE;
 							}else{
 								$lab=$o->getLabel();	
 							}
 					break;
 				}
 				if($lab!=$label){
 					$pass=FALSE;
 				}
 					$count++;
 			}
 		}
		return $pass;
	}

}



?>