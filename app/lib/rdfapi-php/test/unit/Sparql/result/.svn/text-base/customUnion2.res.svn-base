$result = array();

$res1 = new Literal("Alice");
$res2 = new Resource("mailto:alice@work.example");
$res3 = new Resource("mailto:bert@work.example");



$arr["?name"]=$res1;
$arr["?mbox"]=$res2;
$arr1["?name"]="";
$arr1["?mbox"]=$res3;



$result["rowcount"]=2;
$result["hits"]=2;

$result["part"][]=$arr;
$result["part"][]=$arr1;

