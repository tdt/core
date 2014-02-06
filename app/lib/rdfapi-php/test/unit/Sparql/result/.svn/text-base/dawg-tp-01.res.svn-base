$result = array();
$res1 = new Resource("http://example.org/data/p");
$res2 = new Resource("http://example.org/data/v1");
$res3 = new Resource("http://example.org/data/v2");

$arr["?p"]=$res1;
$arr["?q"]=$res2;
$arr1["?p"]=$res1;
$arr1["?q"]=$res3;

$result["rowcount"]=2;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr1;


