$result = array();
$res1 = new Resource("mailto:bob@home");
$res2 = new Resource("mailto:eve@example.net");
$res3 = new Resource("mailto:alice@work");
$res8 = new Resource("mailto:bob@work");
$res9 = new Resource("mailto:fred@edu");

$res5 = new Literal("Alice");
$res6 = new Literal("Eve");
$res7 = new Literal("Bob");


$arr["?name"]=$res7;
$arr["?mbox"]=$res8;


$arr1["?name"]=$res5;
$arr1["?mbox"]=$res3;


$arr2["?name"]=$res6;
$arr2["?mbox"]="";

$arr3["?name"]="";
$arr3["?mbox"]=$res9;

$arr4["?name"]=$res7;
$arr4["?mbox"]=$res1;




$result["rowcount"]=2;
$result["hits"]=5;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;
$result["part"][]=$arr3;
$result["part"][]=$arr4;
