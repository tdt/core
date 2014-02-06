$result = array();
$res1 = new Literal("2004-12-31T18:01:00-05:00");
$res2 = new Literal("2004-01-31T18:01:00-05:00");
$res3 = new Literal("2007-03-12T17:02:00-05:00");
$res4 = new Literal("2007-03-12T17:01:00-05:00");

$res1->setDatatype("http://www.w3.org/2001/XMLSchema#dateTime");
$res2->setDatatype("http://www.w3.org/2001/XMLSchema#dateTime");
$res3->setDatatype("http://www.w3.org/2001/XMLSchema#dateTime");
$res4->setDatatype("http://www.w3.org/2001/XMLSchema#dateTime");



$result = array(
    array('?created' => $res2),
    array('?created' => $res1),
    array('?created' => $res4),
    array('?created' => $res3),
);
