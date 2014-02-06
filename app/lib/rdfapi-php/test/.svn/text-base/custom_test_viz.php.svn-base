<?php

// ----------------------------------------------------------------------------------
// PHP Script: custom_test_viz.php
// ----------------------------------------------------------------------------------

/*
 * This is an online demo of RDF API for PHP.
 * You can paste RDF code into the text field below and choose how the data should be
 * processed. It's possible to parse, serialize, reify and query the data.
 * The size of your RDF code is limited to 10.000 characters, due to resource restrictions.
 *
 * @version $Id$
 * @author Chris Bizer <chris@bizer.de>
 * @autor Seairth Jacobs <seairth@seairth.com>
 * @author Daniel Westphal <dawe@gmx.de>
 * @author Anton Köstlbacher <anton1@koestlbacher.de>
 *
 */

// start outpu buffering
ob_start ();


// Show error message if the rdf is too long
if ((isset($_POST['submit']) AND (strlen($_POST['RDF'])>100000))) {echo "<center><h2>We're sorry, but your RDF is bigger than the allowed size</h2></center>";};




// Test if the form is submitted or the code is too long
if (!isset($_POST['submit']) OR (strlen($_POST['RDF'])>100000)){


////////////////////////////////////////////////////////////////////
// Show input form
////////////////////////////////////////////////////////////////////
?>



<head>
	<title>RAP - RDF API for PHP online demo V0.9.1</title>
	<link href="../doc/phpdoc.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0">
<TR>
  <TD align=left vAlign=top>
    <H3>RDF API for PHP V0.9.1</H3>
    <H1>Online API Demo</H1><BR>


<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<p>This is an online demo of <a href="http://www.wiwiss.fu-berlin.de/suhl/bizer/rdfapi/index.html">RAP - RDF API for PHP V0.9.1</a> . You can paste RDF code into the text field below and choose how the data should be processed. It's possible to reify and query the data.</p>
<p>The size of your RDF code is limited to 10.000 characters, due to resource restrictions.</p>
<H3>Please paste RDF code here:</H3>


<p><textarea cols="100" rows="20" name="RDF"><rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:ex="http://example.org/stuff/1.0/"
xmlns:s="http://description.org/schema/">
<rdf:Description rdf:about="http://www.w3.org/Home/Lassila">
<s:Creator>
<rdf:Description rdf:nodeID="b85740">
<rdf:type rdf:resource="http://description.org/schema/Person"/>
<ex:Name rdf:datatype="http://www.w3.org/TR/xmlschema-2#name">Ora Lassila</ex:Name>
<ex:Email rdf:datatype="http://www.w3.org/TR/xmlschema-2#string">lassila@w3.org</ex:Email>
</rdf:Description>
</s:Creator>
</rdf:Description>
</rdf:RDF></textarea>
        <br />
      </p>
      <H3>Please choose the output format:</H3>
      <table width="70%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td>
              <select name="view_type">
                  <option value="png">PNG</option>
                  <option value="svg">SVG (Plugin needed)</option>
                  <option value="gif">GIF</option>
                  <option value="jpg">JPG</option>
                  <option value="vrml">vrml</option>
                  <option value="input_dot">original DOT</option>
                  <option value="dot">processed DOT</option>
              </select>
            </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><div align="center">
              <input name="saveas" type="checkbox" id="saveas" value="1">
            </div></td>
          <td> <strong>Force "save as...", useful for large graphs</strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><div align="center">
              <input name="prefix" type="checkbox" id="serial_wo_qnames" value="1" checked>
            </div></td>
          <td>Visualize RDF without qnames for RDF tags.</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><div align="center">
              <input name="reify" type="checkbox" id="reify" value="1">
            </div></td>
          <td> <strong>Reify the input model before output.</strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><div align="center"> </div></td>
          <td><strong>Query model (&quot;blank&quot; will match
            anything):</strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><br>
            <table width="99%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="21%" > <div align="left">Subject:</div></td>
                <td width="79%"><input name="query_subject" type="text" id="query_subject2" size="50">
				  <select name="subject_kind" id="object_kind">
                    <option value="resource" selected>Resource</option>
                    <option value="bnode">BlankNode</option>
                  </select></td>
              </tr>
              <tr>
                <td >Predicate:</td>
                <td><input name="query_predicate" type="text" id="query_predicate2" size="50"></td>
              </tr>
              <tr>
                <td >Object:</td>
                <td><input name="query_object" type="text" id="query_object2" size="50">
                  <select name="object_kind" id="object_kind">
                    <option value="resource" selected>Resource</option>
                    <option value="literal">Literal</option>
					<option value="bnode">BlankNode</option>
                  </select>
				  <br>Object datatype: <input name="query_object_datatype" type="text" id="query_object_datatype2" size="47">
				  </td>
              </tr>
            </table></td>
        </tr>
      </table>
      <p><br />
        <br />
        <input type="submit" name="submit" value="submit me!">
      </p>
      </form>



<BR><H1>Feedback</H1>

</p>
    <p>Please send bug reports and other comments to <a href="mailto:chris@bizer.de">Chris Bizer</a>.<br>
</p></body>
</html>
<?php
} else {

/////////////////////////////////////////////////////////////////
// Process RDF
// (if submitted and RDF smaller than 10000 chars)
/////////////////////////////////////////////////////////////////

define("RDFAPI_INCLUDE_DIR", "../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

// Prepare RDF
$rdfInput = stripslashes($_POST['RDF']);

// Create a new MemModel
$model = ModelFactory::getDefaultModel();

// Load and parse document
$model->load($rdfInput);


// Set the base URI of the model
$model->setBaseURI("http://www.semaweb.org".$HTTP_SERVER_VARS['PHP_SELF']."/DemoModel#");


// Execute query on model if submitted
if ($_POST['query_subject']!='' OR $_POST['query_predicate']!='' OR $_POST['query_object']!='') {

	$comment_string="<BR><H3>The following query has been executed:</H3><BR>";

	$query_subj = NULL;
	$query_pred = NULL;
	$query_obj = NULL;

	if ($_POST['query_subject']!='') {
		if($_POST['subject_kind']=='resource'){
			$query_subj = new Resource($_POST['query_subject']); } else {
			$query_subj = new BlankNode($_POST['query_subject']); }
		$comment_string .= "Subject = ".$_POST['query_subject']."<BR>";
	};

	if ($_POST['query_predicate']!='') {
		$query_pred = new Resource($_POST['query_predicate']);
		$comment_string .= "Predicate = ".$_POST['query_predicate']."<BR>";
	};

	if ($_POST['query_object']!='')  {
		if ($_POST['object_kind']=='resource'){
			$query_obj = new Resource($_POST['query_object']); }
		elseif ($_POST['object_kind']=='literal') {
			$query_obj = new Literal($_POST['query_object']);
			if ($_POST['query_object_datatype']!='') {
			$query_obj->setDatatype($_POST['query_object_datatype']); }
			}
		else {
			$query_obj = new BlankNode($_POST['query_object']); };
		$comment_string .= "Object = ".$_POST['query_object']."<BR>";
	};

	// Execute query and display what has been done
   	$model = $model->find($query_subj, $query_pred, $query_obj );
	#echo $comment_string;
}

//  Reify the model if checked in submitted form
if (isset($_POST['reify']) and $_POST['reify']=="1") {
	$model =& $model->reify();
	#echo "<BR><BR><h3>Your original model has been refied.</h3><BR>";
};

if (isset($_POST['prefix']) and $_POST['prefix']=="1") {
	$prefix = TRUE;
}
else
{
	$prefix = FALSE;
};

if (isset($_POST['saveas']) and $_POST['saveas']=="1") {
	$saveas = TRUE;
}
else
{
	$saveas = FALSE;
}



// Output Triples as Visualisation if checked in submitted form

switch ($_POST['view_type'])
{
    case "png":
    header('Content-type: image/png');
    if ($saveas) header('Content-Disposition: attachment; filename=graph.png');
    $model->visualize("png", $prefix);
    break;

    case "svg":
    header('Content-type: image/svg+xml');
    if ($saveas) header('Content-Disposition: attachment; filename=graph.svg');
    $model->visualize("svg", $prefix);
    break;

    case "jpg":
    header('Content-type: image/jpg');
    if ($saveas) header('Content-Disposition: attachment; filename=graph.jpg');
    $model->visualize("jpg", $prefix);
    break;

    case "gif":
    header('Content-type: image/gif');
    if ($saveas) header('Content-Disposition: attachment; filename=graph.gif');
    $model->visualize("gif", $prefix);
    break;

    case "vrml":
    header('Content-type: model/vrml');
    if ($saveas) header('Content-Disposition: attachment; filename=graph.vrml');
    $model->visualize("vrml", $prefix);
    break;

    case "dot":
    header('Content-type: text');
    if ($saveas) header('Content-Disposition: attachment; filename=graph.dot');
    $model->visualize("dot", $prefix);
    break;

    case "input_dot":
    header('Content-type: text');
    if ($saveas) header('Content-Disposition: attachment; filename=graph.dot');
    $model->visualize("input_dot", $prefix);
    break;

}

} // end of "form submitted"


ob_end_flush ();
?>