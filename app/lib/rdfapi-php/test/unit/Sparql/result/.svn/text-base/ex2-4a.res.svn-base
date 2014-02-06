$result = array();
$arr    = array();
$arr1   = array();
$res1 = new Resource("mailto:peter@example.org");
$res2 = new Resource("mailto:jlow@example.com");
$res3 = new Literal("Peter Goodguy");
$res4 = new Literal("Johnny Lee Outlaw");

$arr["?mbox"]=$res1;
$arr["?name"]=$res3;
$arr1["?mbox"]=$res2;
$arr1["?name"]=$res4;

$result["rowcount"]=2;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr1;
