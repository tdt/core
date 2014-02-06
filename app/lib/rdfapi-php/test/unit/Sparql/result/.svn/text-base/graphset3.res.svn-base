$result = array();
$res1 = new Literal("Bob");

$res2 = new Literal("2004-12-06");
$res5 = new Literal("2005-01-10");
$res2->setDatatype("http://www.w3.org/2001/XMLSchema#date");
$res5->setDatatype("http://www.w3.org/2001/XMLSchema#date");

$res3 = new Resource("mailto:bob@oldcorp.example.org");
$res4 = new Resource("mailto:bob@newcorp.example.org");


$arr["?name"]=$res1;
$arr["?mbox"]=$res3;
$arr["?date"]=$res2;

$arr1["?name"]=$res1;
$arr1["?mbox"]=$res4;
$arr1["?date"]=$res5;

$result["rowcount"]=3;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr1;

