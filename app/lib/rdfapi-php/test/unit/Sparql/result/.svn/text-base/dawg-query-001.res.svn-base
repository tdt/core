$result = array();
$res1 = new Resource("mailto:bob@home");
$res2 = new Resource("mailto:eve@example.net");
$res3 = new Resource("mailto:alice@work");
$res8 = new Resource("mailto:bob@work");


$res5 = new Literal("Alice");
$res6 = new Literal("Eve");
$res7 = new Literal("Bob");


$arr["?name"]=$res6;
$arr["?mbox"]="";


$arr1["?name"]=$res5;
$arr1["?mbox"]=$res3;


$arr2["?name"]=$res7;
$arr2["?mbox"]=$res1;

$arr3["?name"]=$res7;
$arr3["?mbox"]=$res8;


$result["rowcount"]=2;
$result["hits"]=4;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;
$result["part"][]=$arr3;
