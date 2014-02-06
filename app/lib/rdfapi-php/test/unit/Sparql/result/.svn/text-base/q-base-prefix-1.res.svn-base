$result = array();



$res1 = new Literal("x:x x:p");
$res2 = new Resource("http://example.org/ns#p");
$res3 = new Resource("http://example.org/x/p");
$res4 = new Literal("d:x ns:p");




$arr1["?v"]=$res1;
$arr1["?p"]=$res3;

$arr2["?v"]=$res4;
$arr2["?p"]=$res2;



$result["rowcount"]=2;
$result["hits"]=2;

$result["part"][]=$arr1;
$result["part"][]=$arr2;




