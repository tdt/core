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


class testMm_setOperations_tests extends UnitTestCase {
	
	function testUnite(){
		$_SESSION['test']='MemModel unite test';
		
		$model1=new MemModel();
		$stat=new Statement(new Resource('http://www.example.org/sub1'),new Resource('http://www.example.org/pred1'),new Resource('http://www.example.org/obj1'));
		$stat2=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
		$stat3=new Statement(new Resource('http://www.example.org/sub3'),new Resource('http://www.example.org/pred3'),new Resource('http://www.example.org/obj3'));
		$model1->add($stat);
		$model1->add($stat2);
				
		$model2=new MemModel();
		$model2->add($stat);
		$model2->add($stat2);
		$model2->add($stat3);
		
		$res=new MemModel();
		$res=$model1->unite($model2);
		
		$this->assertEqual(3,$res->size());
		$this->assertTrue($res->contains($stat));
		$this->assertTrue($res->contains($stat2));
		$this->assertTrue($res->contains($stat3));
	
	}
	
	
	function testIntersect(){
		$_SESSION['test']='MemModel intersect test';
		
		$model1=new MemModel();
		$stat=new Statement(new Resource('http://www.example.org/sub1'),new Resource('http://www.example.org/pred1'),new Resource('http://www.example.org/obj1'));
		$stat2=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
		$stat3=new Statement(new Resource('http://www.example.org/sub3'),new Resource('http://www.example.org/pred3'),new Resource('http://www.example.org/obj3'));
		$model1->add($stat);
		$model1->add($stat2);
				
		$model2=new MemModel();
		$model2->add($stat);
		$model2->add($stat2);
		$model2->add($stat3);
		
		$res=new MemModel();
		$res=$model1->intersect($model1);
		
		$this->assertEqual(2,$res->size());
		$this->assertTrue($res->contains($stat));
		$this->assertTrue($res->contains($stat2));
	
	}
	
	
	function testSubtract(){
		$_SESSION['test']='MemModel subtract test';
		
		$model1=new MemModel();
		$stat=new Statement(new Resource('http://www.example.org/sub1'),new Resource('http://www.example.org/pred1'),new Resource('http://www.example.org/obj1'));
		$stat2=new Statement(new Resource('http://www.example.org/sub2'),new Resource('http://www.example.org/pred2'),new Resource('http://www.example.org/obj2'));
		$stat3=new Statement(new Resource('http://www.example.org/sub3'),new Resource('http://www.example.org/pred3'),new Resource('http://www.example.org/obj3'));
		$model1->add($stat);
		$model1->add($stat2);
				
		$model2=new MemModel();
		$model2->add($stat);
		$model2->add($stat2);
		$model2->add($stat3);
		
		$res=new MemModel();
		$res=$model2->subtract($model1);
		
		$this->assertEqual(1,$res->size());
		$this->assertTrue($res->contains($stat3));
	
	}
	
	function testReify(){
		$_SESSION['test']='MemModel reify test';
		
		$model = new MemModel();
		$model->setbaseURI("http://www.bizer.de");
		
		$myhomepage = new Resource("http://www.bizer.de/welcome.html");
		$creator = new Resource("http://purl.org/dc/elements/1.1/creator");
		$me = new Resource("mailto:chris@bizer.de");
		$model->add(new Statement($myhomepage, $creator, $me));

		$creation_date = new Resource("http://www.example.org/terms/creation-date");
		$August16 = new Literal("August 16, 2002");
		$model->add(new Statement($myhomepage, $creation_date, $August16));

		$language = new Resource("http://www.example.org/terms/language");
		$deutsch = new Literal("Deutsch", "de");
		$model->add(new Statement($myhomepage, $language, $deutsch));

		$model2 =& $model->reify();
		
		$this->assertEqual(12,$model2->size());
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