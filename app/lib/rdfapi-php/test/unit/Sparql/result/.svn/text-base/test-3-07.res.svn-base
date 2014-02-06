$result = array();
$arr    = array();
$arr1   = array();
$arr2   = array();
$arr3   = array();



$res1 = new Blanknode("bNode1");
$res2 = new Resource("http://never/bag");
$res3 = new Literal("11");
$res4 = new Literal("12");
$res5 = new Literal("21");
$res6 = new Literal("22");



$arr["?b"]=$res1;
$arr["?y"]=$res3;

$arr1["?b"]=$res1;
$arr1["?y"]=$res4;

$arr2["?b"]=$res2;
$arr2["?y"]=$res5;

$arr3["?b"]=$res2;
$arr3["?y"]=$res6;

$result["rowcount"]=2;
$result["hits"]=4;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;
$result["part"][]=$arr3;
