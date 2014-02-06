$result = array();
$arr    = array();
$arr1   = array();
$arr2   = array();
$arr3   = array();

$res1 = new Resource("http://example.org/things#xp2");
$res2 = new Resource("http://example.org/things#xd3");
$res3 = new Resource("http://example.org/things#xi2");
$res4 = new Resource("http://example.org/things#xi1");

$res5 = new Literal("1");

$res6 = new Literal("1");
$res6->setDatatype("http://www.w3.org/2001/XMLSchema#double");

$res7 = new Literal("1");
$res7->setDatatype("http://www.w3.org/2001/XMLSchema#integer");

$res8 = new Literal("1");




$arr["?x"]=$res1;
$arr["?v"]=$res5;

$arr1["?x"]=$res2;
$arr1["?v"]=$res6;

$arr2["?x"]=$res3;
$arr2["?v"]=$res7;

$arr3["?x"]=$res4;
$arr3["?v"]=$res8;



$result["rowcount"]=2;
$result["hits"]=4;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;
$result["part"][]=$arr3;
