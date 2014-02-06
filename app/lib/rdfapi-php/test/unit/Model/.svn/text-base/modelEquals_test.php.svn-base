<?php

// ----------------------------------------------------------------------------------
// Class: testModelEquals
// ----------------------------------------------------------------------------------

/**
 * Testcases for the ModelComparator
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

 $_SESSION['PositiveModelEqualstests'] = array(

(1)  => "1" ,
(2)  => "2" ,
(3)  => "3" ,
(4)  => "4" ,
(5)  => "5" ,
(6)  => "6" ,
(7)  => "7" ,
(8)  => "8" 
);

 $_SESSION['NegativeModelEqualstests'] = array(

(1)  => "1" ,
(2)  => "2" ,
(3)  => "3" ,
(4)  => "4" ,
(5)  => "5" 
);
    class testModelEquals extends UnitTestCase {

    	function testMemEqual(){
    		
    		foreach($_SESSION['PositiveModelEqualstests'] as $name){
    			$_SESSION['test']='testfile'.$name.' test';
    			
    			$model1 = ModelFactory::getMemModel();
    			$model2 = ModelFactory::getMemModel();
				
    			$model1->load(MODEL_TESTFILES.'data/testfile'.$name.'_1.nt');
    			$model2->load(MODEL_TESTFILES.'data/testfile'.$name.'_2.nt');
    						
				$this->assertTrue($model1->equals($model2));
    		}
    	}
    
    	function testMemUnequal(){
    		
    		foreach($_SESSION['NegativeModelEqualstests'] as $name){
    			$_SESSION['test']='testfileNegative'.$name.' test';
    			
    			$model1 = ModelFactory::getMemModel();
    			$model2 = ModelFactory::getMemModel();
				
    			$model1->load(MODEL_TESTFILES.'data/testfileNegative'.$name.'_1.nt');
    			$model2->load(MODEL_TESTFILES.'data/testfileNegative'.$name.'_2.nt');
    						
				$this->assertFalse($model1->equals($model2));
    		}
    	}
 	
    
    }
?>