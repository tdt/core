unset($arr1);
unset($arr2);
unset($arr3);

$result = array();

$arr1 = array();
$arr2 = array();
$arr3 = array();

$res1 = new Literal("9");
$res2 = new Literal("Eve");
$res3 = new Resource("http://example.org/dirk01");
$res4 = new Blanknode("bNode4");
$res5 = new Literal("Dirk");
$res6 = new Literal("John");

$res1->setDatatype("http://www.w3.org/2001/XMLSchema#integer");

$arr1["?emp"]=$res1;
$arr1["?name"]=$res2;

$arr2["?emp"]=$res3;
$arr2["?name"]=$res5;

$arr3["?emp"]=$res4;
$arr3["?name"]=$res6;


$result[]=$arr3;
$result[]=$arr2;
$result[]=$arr1;


