$result = array();
$arr    = array();
$arr1   = array();
$arr2   = array();
$arr3   = array();



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
$arr["?z1"]=$res7;
$arr["?z2"]=$res7;

$arr1["?x"]=$res1;
$arr1["?y"]=$res4;
$arr1["?z1"]=$res6;
$arr1["?z2"]=$res6;

$arr2["?x"]=$res1;
$arr2["?y"]=$res3;
$arr2["?z1"]=$res5;
$arr2["?z2"]=$res5;

$arr3["?x"]=$res2;
$arr3["?y"]=$res4;
$arr3["?z1"]=$res8;
$arr3["?z2"]=$res8;






$result["rowcount"]=4;
$result["hits"]=4;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;
$result["part"][]=$arr3;


