<?php

// ----------------------------------------------------------------------------------
// Show_passes.php
// ----------------------------------------------------------------------------------

/**
 * Shows the test results.
 *
 * @version  $Id$
 * @author Tobias Gauß	<tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

    if (!defined('SIMPLE_TEST')) {
        define('SIMPLE_TEST', 'C:/!htdocs/simpletest/');
    }
    require_once(SIMPLE_TEST . 'reporter.php');
    
    class ShowPasses extends HtmlReporter {
        
    
    	function ShowPasses() {
            $this->HtmlReporter();
    
    	}

   		 function paintPass($message) {
      		 // print "<span class=\"pass\">Pass</span>: ";
      		  $breadcrumb = $this->getTestList();
     	 	  array_shift($breadcrumb);
      		//  print implode("-&gt;", $breadcrumb);
      		//  print "->$message<br />\n";
      		//  print "<hr><br>";
      		  parent::paintPass($message);
      		 $_SESSION['passes']++;
   		 }
    	 
    	function paintFail($message) {
    		   $this->paintHeader(" ".$_SESSION['test']); 
  			   parent::paintFail($message);
  			   if(isset($_SESSION['mod1'])&&isset($_SESSION['mod2'])){
  			   $_SESSION['mod1']->writeAsHtmlTable();
  			   $_SESSION['mod2']->writeAsHtmlTable();}
  	 		   $this->paintFooter($_SESSION['test']); 
  			   print "<hr><br>";
  			   $_SESSION['fails']++;
  			   if(LOG){
  			   	$file = fopen ("testlog.log", "a");
  			   	fputs($file,"\r\n".$_SESSION['test'].' fails'."\r\n");
  			   	fclose($file);
  			   }   
  		}
    
    	function _getCss() {
        	return parent::_getCss() . ' .pass { color: green; }';
    	}

    }
?>