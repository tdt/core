<?php
// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

//get a GraphSet from the ModelFactory
$graphset = ModelFactory::getNamedGraphSetMem('NGSet1');

// Create a new NamedGraphsMem
$graph1 = new NamedGraphMem(new Resource('http://graph1'));
$graph2 =& $graphset->createGraph(new Resource('http://graph2'));

//create Quads
$quad1= new Quad(new Resource('http://graph3'),new Resource('http://subject1'),new Resource('http://predicate1'),new Resource('http://object1'));
$quad2= new Quad(new Resource('http://graph3'),new Resource('http://subject2'),new Resource('http://predicate2'),new Resource('http://object2'));
$quad3= new Quad(new Resource('http://graph4'),new Resource('http://subject3'),new Resource('http://predicate3'),new Resource('http://object3'));
$quad5= new Quad(new Resource('http://graph4'),new Resource('http://subject5'),new Resource('http://predicate5'),new Resource('http://object5'));

//add Quads to the GraphSet. Thee needed NamedGraphs will be created automatically
$graphset->addQuad($quad1);
$graphset->addQuad($quad3);
$graphset->addQuad($quad2);

//add a NamedGraph to the Set			  
$graphset->addGraph($graph1);

//Is the Graph "http://graph1" part of the GraphSet?
echo '<b>Is the Graph "http://graph1" part of the GraphSet?</b><BR>';
var_dump($graphset->containsGraph(new Resource('http://graph1')));
echo'<p/>';

//remove 'http://graph2' from the set
$graphset->removeGraph(new Resource('http://graph2'));

//Is the Graph "http://graph2" part of the GraphSet?
echo '<b>Is the Graph "http://graph2" part of the GraphSet?</b><BR>';
var_dump($graphset->containsGraph(new Resource('http://graph2')));
echo'<p/>';

//creat basic statement
$statement=new Statement(new Resource('http://subject4'),new Resource('http://predicate4'),new Resource('http://object4'));

//get a Graph from the Set and add a basic statement
$graph4=&$graphset->getGraph('http://graph4');
$graph4->add($statement);

//remove Quad5
$graphset->removeQuad($quad5);

//get an iterator over all Quads
echo '<p/><b>All Quads:</b><BR>';
for($iterator = $graphset->findQuads(null,null,null,null); $iterator->valid(); $iterator->next()) 
{
	$quad=$iterator->current();
	echo $quad->toString().'<BR>';
};

//get an iterator over all Quads in 'http://graph3'
echo '<p/><b>All Quads in http://graph3:</b><BR>';
for($iterator = $graphset->findQuads(new resource('http://graph3'),null,null,null); $iterator->valid(); $iterator->next()) 
{
	$quad=$iterator->current();
	echo $quad->toString().'<BR>';
};

//how many Quads are in the whole GraphSet
echo '<p/><b>How many Quads are in the whole GraphSet?</b><BR>';
echo $graphset->countQuads();

//does the GraphSet contain the Quad (http://graph4, http://subject4, http://predicate4, http://object14) ?
echo '<p/><b>Does the GraphSet contain the Quad (http://graph4, http://subject4, http://predicate4, http://object14) ?</b><BR>';
var_dump($graphset->containsQuad(new Resource('http://graph4'),new Resource('http://subject4'),new Resource('http://predicate4'),new Resource('http://object4')));
?>