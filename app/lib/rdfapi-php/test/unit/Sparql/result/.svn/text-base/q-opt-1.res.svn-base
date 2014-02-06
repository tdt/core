$result = array();
$res1 = new Resource("mailto:bert@example.net");
$res2 = new Resource("mailto:eve@example.net");
$res3 = new Resource("mailto:alice@example.net");

$res4 = new Literal("Bert");
$res5 = new Literal("Alice");


$arr["?name"]=$res4;
$arr["?mbox"]=$res1;
$arr1["?name"]=$res5;
$arr1["?mbox"]=$res3;
$arr2["?name"]="";
$arr2["?mbox"]=$res2;

$result["rowcount"]=2;
$result["hits"]=3;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;


