$result = array();
unset($arr1);
unset($arr2);
unset($arr3);
unset($arr4);



$res1 = new Literal("Alice");
$res2 = new Literal("Fred");
$res3 = new Literal("Eve");
$res4 = new Literal("Bob");


$res5 = new Resource("mailto:fred@work.example");
$res6 = new Resource("mailto:eve@work.example");
$res7 = new Resource("mailto:alice@work.example");

$arr1["?name"]=$res2;
$arr1["?mbox"]=$res5;

$arr2["?name"]=$res3;
$arr2["?mbox"]=$res6;

$arr3["?name"]=$res1;
$arr3["?mbox"]=$res7;

$arr4["?name"]=$res4;
$arr4["?mbox"]="";




$result[]=$arr4;
$result[]=$arr3;
$result[]=$arr2;
$result[]=$arr1;
