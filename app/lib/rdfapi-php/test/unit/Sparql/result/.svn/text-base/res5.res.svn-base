$result = array();
$res1 = new Blanknode("bNode1");
$res2 = new Blanknode("bNode0");
$res3 = new Literal("30");
$res3->setDatatype("http://www.w3.org/2001/XMLSchema#integer");


$arr["?x"]=$res2;
$arr["?age"]="";

$arr1["?age"]=$res3;
$arr1["?x"]=$res1;

$result["rowcount"]=2;
$result["hits"]=2;
$result["part"][]=$arr;
$result["part"][]=$arr1;



