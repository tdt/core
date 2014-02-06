$result = array();
$arr    = array();
$arr1   = array();



$res1 = new Resource("http://rdf.hp.com/r-1");
$res2 = new Resource("http://rdf.hp.com/r-2");
$res3 = new Resource("http://rdf.hp.com/p-1");
$res4 = new Resource("http://rdf.hp.com/p-2");


$res5 = new Literal("v-1-1");
$res6 = new Literal("v-1-2");
$res7 = new Literal("v-2-1");
$res8 = new Literal("v-2-2");



$arr["?x"]=$res2;
$arr["?y"]=$res3;
$arr["?z"]=$res7;



$arr1["?x"]=$res1;
$arr1["?y"]=$res3;
$arr1["?z"]=$res5;



$result["rowcount"]=3;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr1;



