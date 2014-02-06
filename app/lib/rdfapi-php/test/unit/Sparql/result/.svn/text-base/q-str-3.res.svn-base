$result = array();
$arr    = array();
$arr1   = array();
$arr2   = array();
$arr3   = array();


$res1 = new Resource("http://example.org/things#xp1");
$res2 = new Resource("http://example.org/things#xt1");


$res3 = new Literal("zzz");
$res4 = new Literal("zzz");
$res4->setDatatype("http://example.org/things#myType");



$arr["?x"]=$res1;
$arr["?v"]=$res3;

$arr2["?x"]=$res2;
$arr2["?v"]=$res4;




$result["rowcount"]=2;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr2;

