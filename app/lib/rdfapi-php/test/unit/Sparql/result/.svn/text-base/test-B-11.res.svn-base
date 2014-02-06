$result = array();



$res1 = new Literal("value");
$res1->setLanguage("en");

$res2 = new Resource("http://rdf.hp.com/r");
$res3 = new Resource("http://rdf.hp.com/p2");

$res4 = new Literal("value");
$res4->setDatatype("http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral");

$res5 = new Literal("value");
$res5->setDatatype("http://rdf.hp.com/ns#someType");

$res6 = new Literal("value");
$res6->setLanguage("zz");

$res7 = new Resource("http://rdf.hp.com/p3");
$res8 = new Literal("value");

$res9 = new Resource("http://rdf.hp.com/p8");

$res10 = new Resource("http://rdf.hp.com/p1");

$arr["?z"] =$res1;
$arr["?x"] =$res2;
$arr["?y"] =$res3;

$arr1["?z"] =$res8;
$arr1["?x"] =$res2;
$arr1["?y"] =$res7;

$arr2["?z"] =$res4;
$arr2["?x"] =$res2;
$arr2["?y"] =$res9;

$arr3["?z"] =$res6;
$arr3["?x"] =$res2;
$arr3["?y"] =$res3;

$arr4["?z"] =$res5;
$arr4["?x"] =$res2;
$arr4["?y"] =$res10;

$result["rowcount"]=3;
$result["hits"]=5;

$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;
$result["part"][]=$arr3;
$result["part"][]=$arr4;
