$result = array();
$arr    = array();
$arr1   = array();



$res1 = new Resource("http://rdf.hp.com/r1");
$res2 = new Resource("http://rdf.hp.com/r2");
$res3 = new Literal("r-2-1");
$res4 = new Literal("r-1-2");




$arr["?y"] =$res2;
$arr["?a"] =$res1;
$arr["?b"] =$res3;
$arr["?x"] =$res1;
$arr["?z"] =$res4;

$arr1["?y"] =$res1;
$arr1["?a"] =$res2;
$arr1["?b"] =$res4;
$arr1["?x"] =$res2;
$arr1["?z"] =$res3;


$result["rowcount"]=5;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr1;

