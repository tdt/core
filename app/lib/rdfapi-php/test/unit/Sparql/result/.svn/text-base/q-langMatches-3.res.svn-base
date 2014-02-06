$result = array();
$res1 = new Resource("http://example.org/#p4");
$res2 = new Literal("abc");
$res2->setLanguage("en-gb");

$res3 = new Resource("http://example.org/#p3");
$res4 = new Literal("abc");
$res4->setLanguage("en");

$res5 = new Resource("http://example.org/#p5");
$res6 = new Literal("abc");
$res6->setLanguage("fr");


$arr["?v"]=$res2;
$arr["?p"]=$res1;

$arr1["?v"]=$res4;
$arr1["?p"]=$res3;

$arr2["?v"]=$res6;
$arr2["?p"]=$res5;

$result["rowcount"]=2;
$result["hits"]=3;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;


