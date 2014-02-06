$result = array();
$arr    = array();


$res1 = new Resource("http://example.org/ns#x2");
$res2 = new Literal("false");
$res2->setDatatype("http://www.w3.org/2001/XMLSchema#boolean");



$arr["?a"]=$res1;
$arr["?w"]=$res2;



$result["rowcount"]=2;
$result["hits"]=1;
$result["part"][]=$arr;
