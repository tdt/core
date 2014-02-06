$result = array();
$res1 = new Literal("Bobby");
$res2 = new Literal("Robert");

$res3 = new Resource("http://example.org/foaf/aliceFoaf");
$res4 = new Resource("http://example.org/foaf/bobFoaf");


$arr["?bobNick"]=$res1;
$arr["?src"]=$res3;


$arr2["?bobNick"]=$res2;
$arr2["?src"]=$res4;

$result["rowcount"]=2;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr2;

