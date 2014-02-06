<?php

// ----------------------------------------------------------------------------------
// Class: testVoc
// ----------------------------------------------------------------------------------

/**
* Tests the Vocabularies
*
* <BR><BR>History:<UL>
* <LI>11-23-2004				 : Initial version of this class.
*
* @version  V0.9.1
* @author Tobias Gauﬂ	<tobias.gauss@web.de>
*
* @package unittests
* @access	public
*/


class testVoc extends UnitTestCase {
	var $fks;
	var $vocs;
	var $vocs_res;
	var $element_name;
	var $namespace;
	
	
	/**
	* tests if the vocabularies are consistent
	*
	*/
	function testVocabulary_c(){
		$_SESSION['test']='Vocabulary_C tests';
		$this->_parsLists();
		foreach($this->fks as $key =>$value){
			$temp=new $this->voc[$key]();
			$obj=$temp->$value();
			$this->assertIsA($obj,'Resource');
			$this->assertEqual($obj->getURI(),$this->namespace[$key].$this->element_name[$key]);
			
		}
	}
	
	
	/**
	* tests if the vocabularies are consistent
	*
	*/
	function testVocabulary_res(){
		$_SESSION['test']='Vocabulary_RES tests';
		$this->_parsLists();
		foreach($this->fks as $key =>$value){
			$temp=new $this->voc_res[$key]();
			$obj=$temp->$value();
			$this->assertIsA($obj,'ResResource');
			$this->assertEqual($obj->getURI(),$this->namespace[$key].$this->element_name[$key]);
			
		}

	}
	
	/////////////////////////helper functions///////////////////////////////////////
	
	function _parsLists(){
		unset($this->fks);
		unset($this->namespace);
		unset($this->vocs);
		unset($this->vocs_res);
		
		$IN="vocabulary/vocs.dat";
		$IN2="vocabulary/fks.dat";

		$file = fopen($IN2,'r' );
		while(!feof($file)){
			$line =fgets($file);
			$this->fks[]=trim($line);
		}
		fclose($file);
		

		$file = fopen($IN,'r' );
		$nl = chr(13) . chr(10);
		while(!feof($file)){
			$line =fgets($file);
			$rest= strstr($line,'.');
			$rest=substr($rest,1);
			$head=strchr($rest,"'");
			$pos=strpos($head,";");
			$this->element_name[]=substr($head,1,$pos-2);
			$pos=strpos($line,'.');
			$namespaceString=trim(substr($line,0,$pos));
			$this->namespace[]=trim(constant($namespaceString));
			$pos=strpos($namespaceString,'_');
			if($namespaceString[$pos+2]=='C'){
				$this->voc_res[]='RDFS_RES';
				$this->voc[]='RDFS';
			}
			else{
				$this->voc_res[]=trim(substr($namespaceString,0,$pos).'_RES');
				$this->voc[]=trim(substr($namespaceString,0,$pos));
			}
		}
	
	
	}
	
	

}

 ?>