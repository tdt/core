$result = array();
$res1 = new Resource("mailto:bert@example.net");
$res2 = new Resource("mailto:eve@example.net");
$res3 = new Resource("mailto:alice@example.net");

$res4 = new Literal("Bert");
$res5 = new Literal("Alice");

$res6 = new Literal("WhoMe?");
$res7 = new Literal("DuckSoup");


$arr["?name"]=$res4;
$arr["?mbox"]=$res1;
$arr["?nick"]="";

$arr1["?name"]=$res5;
$arr1["?mbox"]=$res3;
$arr1["?nick"]=$res6;

$arr2["?name"]="";
$arr2["?mbox"]=$res2;
$arr2["?nick"]=$res7;

$result["rowcount"]=3;
$result["hits"]=3;
$result["part"][]=$arr;
$result["part"][]=$arr1;
$result["part"][]=$arr2;

