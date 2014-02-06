<?php
// ----------------------------------------------------------------------------------
// PHP Script: custom_rdql_test.php
// ----------------------------------------------------------------------------------

/*
 * This is an online demo of RAP's RDQL engine.
 * Input an RDQL query string and the engine will query the document
 * specified in the source clause.
 *
 * @author Radoslaw Oldakowski <radol@gmx.de>
 * @version $Id$
 */
function getmicrotime()
{
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
}       

        $start = getmicrotime();


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>RAP's RDQL-Engine online demo</TITLE>
<META content="text/html; charset=iso-8859-1" http-equiv=Content-Type>
<LINK href="../doc/phpdoc.css" rel=stylesheet type=text/css>
</HEAD>

<BODY>
<TABLE border=0 >
  <TBODY>
  <TR>
    <TD align=left vAlign=top>
      <DIV align=right><BR>
	    &nbsp;
		<A href="http://www.w3.org/RDF/" target=_blank>
	      <IMG alt="RDF Logo" border=0 height=40 src="../doc/rdf_metadata_button.gif" width=95>
		</A>
		&nbsp;
		<A href="http://www.php.net/" target=_blank>
		  <IMG alt="PHP Logo" border=0 height=64 src="../doc/php_logo.gif" width=120>
		</A>
	  </DIV>
      <H3>RDF API for PHP V0.8</H3>
      <H1>RDQL-Engine Online Demo</H1><BR>
      <P>This is an online demo of <A href="http://www.wiwiss.fu-berlin.de/suhl/bizer/rdfapi/index.html">
	     RAP - RDF API for PHP V0.8</A>.<br>
<?php
// Test if the form is submitted or the query_string is too long
if (!isset($_POST['submit']) OR (strlen($_POST['query_string'])>1000)) {

   // Show error message if the rdf is too long
   if ((isset($_POST['submit']) AND (strlen($_POST['query_string'])>1000))) {
       echo "<center><a href='" .$HTTP_SERVER_VARS['PHP_SELF'] ."'><h2>Go back to input form.</h2></a></center>";
       echo "<center><p class='rdql_comment'>We're sorry, but your RDQL query is bigger than the allowed size of 1000 characters</p></center>";
   };

?>
<form method="post" action="<?php echo $HTTP_SERVER_VARS['PHP_SELF']; ?>">
      Paste an <a href="./../doc/rdql_grammar.htm">RDQL</a> query string into the text field below.
      In the FROM clause you can indicate an URL or a path for local RDF document to be queried.
	  </P>
      <H3>Please paste your RDQL query here:</H3>
      <P><TEXTAREA cols=80 name=query_string rows=15>
/* Find the name of the creator of <http://www.w3.org/Home/Lassila> */
/* ---------------------------------------------------------------- */

SELECT ?Name
/* --- Input file --- */
FROM <Example1.rdf>
WHERE (?x, desc:Creator, ?z)
      (?z, ex:Name, ?Name)
AND ?x eq <http://www.w3.org/Home/Lassila>
USING desc FOR <http://description.org/schema/>
      ex FOR <http://example.org/stuff/1.0/>

         </TEXTAREA> <BR>
	  </P>
      <H3>Please choose the output format:</H3>
          <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
            <TBODY>
              <TR>
                <TD> <DIV align=center>
                    <INPUT id="show_input" name="show_input"
            type=checkbox value=1>
                  </DIV></TD>
                <TD><STRONG>Show the source model</STRONG> (only if it contains
                  fewer than 100 statements)</TD>
              </TR>
              <TR>
                <TD>&nbsp;</TD>
                <TD>&nbsp;</TD>
              </TR>
            </TBODY>
          </TABLE>
      <P><INPUT name=submit type=submit value="submit me!">
      </P></FORM><BR>
<?php
} else {

// Process the query if submitted
define("RDFAPI_INCLUDE_DIR", "./../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(RDFAPI_INCLUDE_DIR . "rdql/RDQL.php");

echo "<center><a href='" .$HTTP_SERVER_VARS['PHP_SELF'] ."'>
         <h2>Go back to input form.</h2></a></center>";

if (isset($_POST['query_string'])) {

   $queryString=stripslashes($_POST['query_string']);

   // Parse the query
   $parser = new RdqlParser();
   $parsed = & $parser->parseQuery($queryString);

   // If more than one source file provided show an error message
   if (count($parsed['sources']) > 1) {
      echo "<center><p class='rdql_comment'>We're sorry, but this Online Demo allows you to query only one document</p></center>";
   }

// Create a new MemModel
$model = ModelFactory::getDefaultModel();

// Load and parse document
$model->load($parsed['sources'][0]);


   // Process the query
   $engine = new RdqlMemEngine();
   $queryResult = $engine->queryModel($model, $parsed, TRUE);
   
   // Show the query string
   echo "<br><h3>Your query: </h3>";
   echo "<table width='100%' bgcolor=#e7e7ef><tr><td>";
   echo "<p bgcolor='34556'><code>" .nl2br(htmlspecialchars(stripslashes($_POST['query_string']))) ."</code></p>";
   echo "</td></tr></table><br>";
   
   // Show query result
   echo "<br><h3>Query result: </h3>";
   $engine->writeQueryResultAsHtmlTable($queryResult);
   
   // Show the input model if option chosen
   if (isset($_POST['show_input']) && $_POST['show_input'] == "1") {
      echo "<br><br><h3>Source model: </h3>";
      $model->writeAsHtmlTable();
   }

}
}
echo "<br><br><br>";
?>

<BR><BR>
<?php
echo "execution took: ".(getmicrotime() - $start)." seconds\n";
?>


      <H1>Feedback</H1>
      <P></P>
      <p>Please send bug reports and other comments to <a href="mailto:radol@gmx.de">Radek Oldakowski</a>.<br></p>

	</TR>
    </TBODY>
   </TABLE>
  </BODY>
 </HTML>

