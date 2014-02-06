$result = array();



$res1 = new Resource("http://example.org/things#xd3");
$res2 = new Resource("http://example.org/things#xd1");
$res3 = new Resource("http://example.org/things#xd2");

$res4 = new Literal("1");
$res4->setDatatype("http://www.w3.org/2001/XMLSchema#double");

$res5 = new Literal("1.0");
$res5->setDatatype("http://www.w3.org/2001/XMLSchema#double");

$res6 = new Literal("1.0");
$res6->setDatatype("http://www.w3.org/2001/XMLSchema#double");

$arr["?x"]=$res1;
$arr["?v"]=$res4;

$arr1["?x"]=$res2;
$arr1["?v"]=$res5;

$arr2["?x"]=$res3;
$arr2["?v"]=$res6;




$result["rowcount"]=2;
$result["hits"]=3;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;

