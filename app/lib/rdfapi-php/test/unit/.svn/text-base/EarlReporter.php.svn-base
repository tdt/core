<?php
/**
*   EARL reporter for SimpleTest
*
*   @author Christian Weiske <cweiske@cweiske.de>
*   @see    http://www.w3.org/2001/sw/DataAccess/tests/earl
*/
require_once SIMPLETEST_INCLUDE_DIR . 'simpletest.php';
require_once SIMPLETEST_INCLUDE_DIR . 'reporter.php';
require_once RDFAPI_INCLUDE_DIR . 'model/MemModel.php';
require_once RDFAPI_INCLUDE_DIR . 'syntax/N3Serializer.php';


class EarlReporter extends SimpleReporter
{
    const EARL = 'http://www.w3.org/ns/earl#';
    const FOAF = 'http://xmlns.com/foaf/0.1/';
    const RDF  = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
    const RDFS = 'http://www.w3.org/2000/01/rdf-schema#';
    const DOAP = 'http://usefulinc.com/ns/doap#';

    protected $model      = null;
    protected $serializer = null;

    /**
    *   Set via $testcase->signal('earl:test', 'mytestname')
    */
    protected $currentTestName = null;



    public function __construct($serializer = null, $model = null)
    {
        if ($serializer !== null) {
            $this->serializer = $serializer;
        } else {
            $this->serializer = new N3Serializer();
        }

        if ($model !== null) {
            $this->model = model;
        } else {
            $this->model = new MemModel();
        }

        if (!isset($GLOBALS['earlReport'])) {
            die('Please configure $GLOBALS[\'earlReport\'] as shown in config.php.dist' . "\n");
        }
    }//public function __construct($serializer = null, $model = null)



    function paintHeader($test_name)
    {
        //add personal information about the asserter
        $this->assertingPerson = new Resource($GLOBALS['earlReport']['reporter']['seeAlso'] . '#me');
        $this->model->add(new Statement(
            $this->assertingPerson,
            new Resource(self::RDF  . 'type'),
            new Resource(self::FOAF . 'Person')
        ));
        $this->model->add(new Statement(
            $this->assertingPerson,
            new Resource(self::RDFS . 'seeAlso'),
            new Resource($GLOBALS['earlReport']['reporter']['seeAlso'])
        ));
        $this->model->add(new Statement(
            $this->assertingPerson,
            new Resource(self::FOAF . 'homepage'),
            new Resource($GLOBALS['earlReport']['reporter']['homepage'])
        ));
        $this->model->add(new Statement(
            $this->assertingPerson,
            new Resource(self::FOAF . 'name'),
            new Literal($GLOBALS['earlReport']['reporter']['name'])
        ));


        //project information
        $this->project = new Resource('http://rdfapi-php.sf.net/');
        $this->model->add(new Statement(
            $this->project,
            new Resource(self::RDF . 'type'),
            new Resource(self::DOAP . 'Project')
        ));
        $this->model->add(new Statement(
            $this->project,
            new Resource(self::DOAP . 'name'),
            new Literal('RDF API for PHP')
        ));
        $version = new BlankNode($this->model);
        $this->model->add(new Statement(
            $this->project,
            new Resource(self::DOAP . 'release'),
            $version
        ));
            $this->model->add(new Statement(
                $version,
                new Resource(self::RDF . 'type'),
                new Resource(self::DOAP . 'Version')
            ));
            $this->model->add(new Statement(
                $version,
                new Resource(self::DOAP . 'created'),
                new Literal(date('Y-m-d H:i'), null, 'http://www.w3.org/2001/XMLSchema#date')
            ));
            $this->model->add(new Statement(
                $version,
                new Resource(self::DOAP . 'name'),
                new Literal('RAP SVN-' . date('Y-m-d\\TH:i'))
            ));
    }//function paintHeader($test_name)



    function paintFooter($test_name)
    {
        $this->serializer->addNSPrefix(self::DOAP, 'doap');
        $this->serializer->addNSPrefix(self::EARL, 'earl');
        $this->serializer->addNSPrefix(self::FOAF, 'foaf');

        $this->serializer->addNoNSPrefix('http://rdfapi-php.sf.net/');
        $this->serializer->addNoNSPrefix($GLOBALS['earlReport']['reporter']['homepage']);

        $this->serializer->setCompress(true);
        $this->serializer->setPrettyPrint(true);
        $this->serializer->setNest(true);

        echo $this->serializer->serialize(
            $this->model
        );
    }



    /**
    *   We use this to keep track of test titles
    */
    function paintSignal($type, $payload)
    {
        switch ($type) {
            case 'earl:name':
                $this->currentTestName = $payload;
                break;
            default:
                echo "Unknown signal type $type\n";
                break;
        }
    }//function paintSignal($type, $payload)


    function paintStart($test_name, $size) {
        parent::paintStart($test_name, $size);
    }

    function paintEnd($test_name, $size) {
        parent::paintEnd($test_name, $size);
    }

    function paintPass($message) {
        $this->addTest(true);
//        echo 'pass: ' . $message . "\n";
        parent::paintPass($message);
    }

    function paintFail($message) {
        $this->addTest(false);
//        echo 'fail: ' . $message . "\n";
        parent::paintFail($message);
    }

    function addTest($bPass)
    {
        if ($this->currentTestName === null) {
//            echo "No test name set! Ignoring test\n";
            return;
        }

        $assertion = new BlankNode($this->model);
        $this->model->add(new Statement(
            $assertion,
            new Resource(self::RDF  . 'type'),
            new Resource(self::EARL . 'Assertion')
        ));
        $this->model->add(new Statement(
            $assertion,
            new Resource(self::EARL . 'assertedBy'),
            $this->assertingPerson
        ));

        $result = new BlankNode($this->model);
        $this->model->add(new Statement(
            $assertion,
            new Resource(self::EARL . 'result'),
            $result
        ));
            $this->model->add(new Statement(
                $result,
                new Resource(self::RDF  . 'type'),
                new Resource(self::EARL . 'TestResult')
            ));
            $this->model->add(new Statement(
                $result,
                new Resource(self::EARL . 'outcome'),
                new Resource(self::EARL . ($bPass ? 'pass' : 'fail'))
            ));
        $this->model->add(new Statement(
            $assertion,
            new Resource(self::EARL . 'subject'),
            $this->project
        ));
        $this->model->add(new Statement(
            $assertion,
            new Resource(self::EARL . 'test'),
            new Resource($this->currentTestName)
        ));


        $this->currentTestName = null;
    }//function addTest($bPass)

}//class EarlReporter extends SimpleReporter

?>