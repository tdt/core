<?php

/**
 * This class handles a database resource, currently supported databases are the ones handles by the redbeans ORM
 *
 * @package The-Datatank/custom/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\strategies;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RedBean_Facade as R;
use tdt\core\model\resources\read\IFilter;
use tdt\core\universalfilter\interpreter\other\QueryTreeHandler;
use tdt\core\utility\Config;
use tdt\exceptions\TDTException;

class DB extends ATabularData implements IFilter {
    /*
     * If no limit is defined, get a default maximum of rows
     * This way reading a database with a large set of records will not cause
     * the PHP execution to crash of memory allocation.
     */

    private static $READ_MAX_AMOUNT_OF_ROWS = 150;
    //lowercase engine names
    private static $supportedEngines = array("mysql");

    public function __construct() {
        parent::__construct();
        $this->parameters["columns"] = "An array that contains the name of the columns that are to be published, if an empty array is passed every column will be published. This array should be build as column_name => column_alias.";
    }

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("db_type", "location", "db_table", "username", "password");
    }

    /**
     * The parameters ( array keys ) returned all of the parameters that can be used to create a strategy.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateParameters() {
        $this->parameters["username"] = "The username to connect to the database with. This is required except for SQLite engines.";
        $this->parameters["password"] = "The password of the user to connect to the database. This is required except for SQLite engines.";
        $this->parameters["db_name"] = "The database name, all except sqlite needs to fill in this parameter.";
        $this->parameters["db_type"] = "The type of the database, current supported types are: MySQL";
        $this->parameters["db_table"] = "The database table of which some or all fields will be published.";
        $this->parameters["location"] = "The location of the database, for sqlite this will be the path towards the sqlite file, for all the other database types this will be the host on which the database is installed.";
        $this->parameters["PK"] = "The primary key of an entry. This must be the name of an existing column name in the tabular resource.";
        $this->parameters["limit"] = "The limit of a data retrieval query on the database table. If not provided a default amount of rows will be loaded.";
        return $this->parameters;
    }

    /**
     * Returns an array with parameter => documentation pairs that can be used to read a CSV resource.
     * @return array with parameter => documentation pairs
     */
    public function documentReadParameters() {
        return array();
    }

    /**
     * Read a resource
     * @param $configObject The configuration object containing all of the parameters necessary to read the resource.
     * @param $package The package name of the resource
     * @param $resource The resource name of the resource
     * @return $mixed An object created with fields and values of the database table
     */
    public function read(&$configObject, $package, $resource) {
        parent::read($configObject, $package, $resource);

        R::addDatabase('db_resource', $configObject->db_type . ":host=" . $configObject->location . ";dbname=" . $configObject->db_name, $configObject->username, $configObject->password);
        R::selectDatabase('db_resource');

        $fields = ""; //implode(array_keys($configObject->columns),",");

        foreach ($configObject->column_aliases as $column_name => $column_alias) {
            $fields.= " $configObject->db_table" . "." . "$column_name AS $column_alias ,";
        }

        $fields = rtrim($fields, ",");

        // prepare to get some of them data from the database!
        $sql_limit = "";

        if ($configObject->limit != "") {
            $sql_limit = "LIMIT 0,$configObject->limit";
        } else {
            $sql_limit = "LIMIT 0, " . DB::$READ_MAX_AMOUNT_OF_ROWS;
        }
        $sql = "SELECT $fields FROM $configObject->db_table $sql_limit";

        $results = R::getAll($sql);

        /*
         *  The result of the R::getAll results in an array of arrays. Each array represents a row.
         * Loop over them and build up the resulting object to be returned, use the PK and the column_aliases as well.
         */

        $PK = $configObject->PK;
        $aliases = $configObject->column_aliases;

        $arrayOfRowObjects = array();

        foreach ($results as $result) {

            $rowobject = new \stdClass();

            // get the data out of the row and create an object out of it
            foreach ($aliases as $table_column => $alias) {
                $rowobject->$alias = $result[$alias];
            }

            /**
             * Add the object to the array of row objects
             */
            if ($PK == "") {
                array_push($arrayOfRowObjects, $rowobject);
            } else {
                if (!isset($arrayOfRowObjects[$rowobject->$PK]) && $rowobject->$PK != "") {
                    $arrayOfRowObjects[$rowobject->$PK] = $rowobject;
                } elseif (isset($arrayOfRowObjects[$rowobject->$PK])) {
                    // this means the primary key wasn't unique !
                    $log = new Logger('DB');
                    $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                    $log->addAlert("Resource $package / $resources : Primary key " . $rowobject->$PK . " isn't unique.");
                } else {
                    // this means the primary key field was empty, log the problem and continue
                    $log = new Logger('DB');
                    $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                    $log->addAlert("Resource $package / $resources : Primary key " . $rowobject->$PK . " is empty.");
                }
            }
        }
        R::selectDatabase('default');
        return $arrayOfRowObjects;
    }

    protected function isValid($package_id, $generic_resource_id) {
        /**
         * Check if parameters for non sqlite engines are all passed, create the connection string
         * check if a connection can be made, check if the columns (if any are passed) are
         * existing ones in the database, if not get the columns from the datatable
         */
        if (!isset($this->username)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("You have to pass along a username for your database resource configuration."), $exception_config);
        }

        if (!isset($this->password)) {
            $this->password = "";
        }

        if (!isset($this->location)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("You have to pass along the location of the database of which you want to open up a table."), $exception_config);
        }

        if (!isset($this->column_aliases)) {
            $this->column_aliases = array();
        }

        /**
         * Check if there is a ";" passed in the table parameter, if so give back an error
         */
        if (strpos($this->db_table, ";") != FALSE) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Your database table has a semi-colon in it, this is not allowed!"), $exception_config);
        }

        /**
         * validate according to the db engine
         */
        if (!isset($this->db_type)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("The database engine i.e. MySQL."), $exception_config);
        } else {
            // check if the db_type is supported
            $this->db_type = strtolower($this->db_type);
            if (!in_array($this->db_type, DB::$supportedEngines)) {
                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(452, array("Your database engine, $this->db_type, is not supported."), $exception_config);
            }
        }

        /*
         * Check if a database name is given
         */
        if (!isset($this->db_name)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Passing a database name, parameter db_name, is required!"), $exception_config);
        }

        /**
         * Now we're going to check if the columns passed are present in the table.
         * 1) Prepare the connection
         * 2) Get the columnnames from the table
         * 3) Check if the passed columns are in the set of column names gotten from the table
         *    a. if no columns are passed, push all of the column names into the $this->columns array
         * 4) If the columns are all A-OK! then return true.
         * All this functionality has been put into functions.
         */
        // prepare the connection
        R::addDatabase('db_resource', $this->db_type . ":host=" . $this->location . ";dbname=" . $this->db_name, $this->username, $this->password);
        R::selectDatabase('db_resource');
        // get the table columns
        $table_columns = $this->getTableColumns();
        R::selectDatabase('default');
        $this->validateColumns($table_columns);
        return true;
    }

    /**
     * Check if the columns passed are in the table
     * if no columns are passed, then fill up the $this->columns with the columns gotten from the table
     */
    private function validateColumns($table_columns) {
        if (!isset($this->columns)) {

            $this->columns = array();
            $index = 0;
            foreach ($table_columns as $column) {
                $this->columns[$index] = $column;
                $index++;
            }
        } else {

            //$this->columns = array();
            foreach ($this->columns as $column_key => $column_value) {
                if (!in_array($column_value, $table_columns)) {
                    //throw error
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(452, array("The supplied column, $column_value, does not exist."), $exception_config);
                }
            }
            // make the columns as columnname => columnname
            // then in the second foreach put the aliases in the columns array (which technically is a hash)
            foreach ($table_columns as $index => $column) {
                if (!is_numeric($index)) {
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(452, array("The index $index is not numeric in the columns array!"), $exception_config);
                }
            }
        }

        return true;
    }

    /**
     * This function gets the names for the columns of a certain database table
     * In this case the database table that was passed via the db_table parameter.
     */
    private function getTableColumns() {
        return array_keys(R::getRow("SELECT * FROM $this->db_table"));
    }

    /**
     * This function in this database resource forms an example implementation of how to handle
     * and execute partial trees of the query tree.
     *
     * Because we can actually perform everything the AST (query tree ) throws at us we're going to assume
     * we cannot execute a sort by or higher order statements. Thus we can only go as far as select ( includes where and group by clause )
     *
     */
    public function readAndProcessQuery($query, $parameters) {

        /*
         * Decide which part of the query we want to execute ourselves and which part we leave to the universalinterpreter
         * Since this database resource serves as an example for how partially execution works we're going to assume we
         * cannot perform sort by statements ( or higher such as limit statements).
         * Thus we ask for the QueryTreeHandler for the clauses up untill select.
         */

        $queryHandler = new QueryTreeHandler($query);
        $converter = $queryHandler->getSqlConverter();

        $queryNode = $queryHandler->getNodeForClause("select");

        $selectClause = $converter->getSelectClause();
        $whereClause = $converter->getWhereClause();
        $groupByClause = $converter->getGroupByClause();


        /*
         * get the configuration object to read a DB resource
         * and get the extra information such as columns and primary key from the parent of the DB resource.
         *
         * Apply logic to assess whether or not you can execute a clause of the converted querytree
         *
         * THERE ARE 3 POSSIBLE ROUTES AN IMPLEMENTATION OF A IFILTER CAN WALK
         *
         * example query: select ... from ... where group by sort by
         *
         * 1) Don't execute anything of the query, you cannot do anything with the given clauses
         *      This is a failsafe, when something goes wrong along the way, let the universalInterpreter handle the entire query tree
         * 2) Execute the entire query tree and update the tree with an externally calculated node
         *      Let the universal interpreter know that you have fully done your job by setting the conventional parameters to the resulting object
         *      (See lower lines of this function implementation)
         * 3) Execute a partial tree of this query tree
         *      Get the query node you want to execute, execute it on w/e data source you want to, in this case a database.
         *      Create an array of resulting objects, and apply the conventional parameters to the resulting object.
         *      So that the universalInterpreter knows you have partially executed a clause of the query tree.
         *
         * Note that we currently support the replacement of a where, group by, select and order by clause of the query tree.
         *
         */

        /*
         * Get the configObject
         */
        $configObject = $parameters["configObject"];
        parent::read($configObject, $parameters["package"], $parameters["resource"]);

        // replace the source with the actual database table
        $sourceIdentifier = $parameters["package"] . "." . $parameters["resource"];
        $sourceIdentifier = str_replace("/", ".", $sourceIdentifier);

        // initialize the ORM redbeans to execute some SQL
        R::setup($configObject->db_type . ":host=" . $configObject->location . ";dbname=" . $configObject->db_name, $configObject->username, $configObject->password);

        $resultObject = new \stdClass();

        $sql = $this->convertClausesToSQLString($converter, $configObject);

        try {
            /*
             * Execute the query
             * We know that we don't execute the order by statement, thus after putting together the object
             * we put together an array with the clauses used, and the phpObject retrieved from the database query.
             * We also put an empty entry for the order by clause (if given) so that the interpreter knows we didn't execute it.
             */
            $results = R::getAll($sql);

            $arrayOfRowObjects = array();
            /*
             * assemble objects out of the resultset
             */
            foreach ($results as $result) {

                $rowobject = new \stdClass();

                // get the data out of the row and create an object out of it
                $rowKeys = array_keys($result);
                foreach ($rowKeys as $key) {
                    $rowobject->$key = $result[$key];
                }

                /**
                 * Add the object to the array of row objects
                 */
                array_push($arrayOfRowObjects, $rowobject);
            }

            /*
             * We added all the possible clauses except for the order by clause in our SQL query.
             * That means that unless there's been an order by clause, we have executed the entire query.
             * Thus, we either return the resultobject, that will replace the entire query, or pass along the clauses we have executed
             * and the ones that have not. And let the interpreter rebuild his query for further execution.
             */
            if ($converter->getOrderByClause()) {
                /*
                 * This is path 3 (see commentary of this function)
                 * indexInParent and phpDataObject are put there to notify the universalTableManager that
                 * we partially executed the query. This convention can be changed into something more appropriate.
                 */
                $resultObject->indexInParent = "";

                /*
                 *  We have executed the select partial tree, notify this to the universalfilterTableManager
                 */
                $resultObject->clause = "select";
                $resultObject->partialTreeResultObject = $arrayOfRowObjects;

                $resultObject->query = $query;
            } else {
                /*
                 * This is path 2
                 */
                $resultObject->indexInParent = "-1";
                $resultObject->executedNode = $query;
                $resultObject->parentNode = null;
                $resultObject->phpDataObject = $arrayOfRowObjects;
            }
        } catch (Exception $ex) {
            /*
             *  This is path 1
             * If something goes wrong, just let the interpreter handle the query.
             */
            $resultObject->indexInParent = "";
            $resultObject->executeNode = NULL;
            $resultObject->phpDataObject = NULL;
            $resultObject->parentNode = null;
        }

        return $resultObject;
    }

    /*
     * put together the sql string
     * The converter contains functionality to get the clauses of the query tree.
     */

    private function convertClausesToSQLString($converter, $configObject) {
        /*
         * put together an sql query based on the clauses.
         */

        // select clause
        $sql = "SELECT ";
        foreach ($converter->getSelectClause() as $column) {
            $sql.= $column . ", ";
        }

        $sql = rtrim($sql, ", ");
        $sql.= " FROM " . $configObject->db_table . " ";

        // where clause
        if ($converter->getWhereClause()) {
            $sql.= " WHERE ";
            foreach ($converter->getWhereClause() as $whereclause) {
                $sql.= $whereclause;
            }
        }

        //group by
        if ($converter->getGroupByClause()) {
            $sql.= " GROUP BY ";
            foreach ($converter->getGroupByClause() as $groupbyidentifier) {
                $sql.= $groupbyidentifier . ", ";
            }
        }

        $sql = rtrim($sql, ", ");

        return $sql;
    }

}

?>
