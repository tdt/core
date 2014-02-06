$result = array();
$arr    = array();

$res3 = new Literal("TITLE 1");
$res4 = new Literal("10");
$res4->setDatatype("http://www.w3.org/2001/XMLSchema#integer");

$res5 = new Literal("TITLE 3");


$arr["?title"]=$res3;
$arr["?price"]=$res4;

$arr1["?title"]=$res5;
$arr1["?price"]="";

$result["rowcount"]=2;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr1;