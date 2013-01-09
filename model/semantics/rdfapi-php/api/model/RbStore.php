<?php

/**
 * Extension of the DbStore Class from RAP API to support Redbean
 *
 * @package The-Datatank/model/semantics
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */
class RbStore extends DbStore {

    //Constructor has to be here to disable parent constructor
    public function RbStore() {

        //parent::DbStore($dbDriver, $host, $dbName, $user, $password);
    }

    protected function _createTables_MySQL() {

        R::begin();


        $check = true;
        try {

            $check = $check && !is_null(R::exec("CREATE TABLE IF NOT EXISTS models
                                (modelID bigint NOT NULL,
                                    modelURI varchar(255) NOT NULL,
                                    baseURI varchar(255) DEFAULT '',
                                    primary key (modelID))"));

            $check = $check && !is_null(R::exec('CREATE UNIQUE INDEX m_modURI_idx ON models (modelURI)'));

            $check = $check && !is_null(R::exec("CREATE TABLE IF NOT EXISTS statements
                                (modelID bigint NOT NULL,
                                    subject varchar(255) NOT NULL,
                                    predicate varchar(255) NOT NULL,
                                    object text,
                                    l_language varchar(255) DEFAULT '',
                                    l_datatype varchar(255) DEFAULT '',
                                    subject_is varchar(1) NOT NULL,
                                    object_is varchar(1) NOT NULL)"));

            $check = $check && !is_null(R::exec("CREATE TABLE IF NOT EXISTS namespaces
                                (modelID bigint NOT NULL,
                                    namespace varchar(255) NOT NULL,
                                    prefix varchar(255) NOT NULL,
                                    primary key (modelID,namespace))"));

            $check = $check && !is_null(R::exec("CREATE TABLE IF NOT EXISTS `dataset_model` (
                                    `datasetName` varchar(255) NOT NULL default '0',
                                    `modelId` bigint(20) NOT NULL default '0',
                                    `graphURI` varchar(255) NOT NULL default '',
                                    PRIMARY KEY  (`modelId`,`datasetName`))"));

            $check = $check && !is_null(R::exec("CREATE TABLE IF NOT EXISTS `datasets` (
                                    `datasetName` varchar(255) NOT NULL default '',
                                    `defaultModelUri` varchar(255) NOT NULL default '0',
                                    PRIMARY KEY  (`datasetName`),
                                    KEY `datasetName` (`datasetName`))"));

            $check = $check && !is_null(R::exec('CREATE INDEX s_mod_idx ON statements (modelID)'));
            $check = $check && !is_null(R::exec('CREATE INDEX n_mod_idx ON namespaces (modelID)'));

            $check = $check && !is_null(R::exec('CREATE INDEX s_sub_pred_idx ON statements
                                (subject(200),predicate(200))'));

            $check = $check && !is_null(R::exec('CREATE INDEX s_sub_idx ON statements (subject(200))'));
            $check = $check && !is_null(R::exec('CREATE INDEX s_pred_idx ON statements (predicate(200))'));
            $check = $check && !is_null(R::exec('CREATE INDEX s_obj_idx ON statements (object(250))'));

            $check = $check && !is_null(R::exec('CREATE INDEX s_obj_ftidx ON statements (object(250))'));
        } catch (Exception $ex) {
            throw new DatabaseTDTException($ex->getMessage());
            R::rollback();
        }
        if (!$check) {
            throw new DatabaseTDTException("Tables are not created");
            R::rollback();
        } else
            R::commit();
        return $check;
    }


    protected function _createUniqueModelID() {
        $maxModelID = R::getRow('SELECT MAX(modelID) mx FROM models');

        return++$maxModelID['mx'];
    }


    public function close() {
        unset($this);
    }

    public function getModel($modelURI) {
        if (!$this->modelExists($modelURI))
            return FALSE;
        else {
            
            $param = Array(':modelURI' => $modelURI);
            $modelVars = R::getRow("SELECT modelURI, modelID, baseURI
                                            FROM models WHERE modelURI=:modelURI", $param);
             
            return new RbModel($modelVars['modelURI'], $modelVars['modelID'], $modelVars['baseURI']);
        }
    }

    public function getNewModel($modelURI, $baseURI = NULL) {
        if ($this->modelExists($modelURI))
            return FALSE;
        else {
            $modelID = $this->_createUniqueModelID();
            $params = array('modelID' => $modelID, 'modelURI' => $modelURI, 'baseURI' => $baseURI);
            $rs = R::exec("INSERT INTO models (modelID, modelURI, baseURI) VALUES (:modelID, :modelURI, :baseURI)", $params);

            if (!$rs)
                throw new DatabaseTDTException("New RDF Model not created");
            else
                return new RbModel($modelURI, $modelID, $baseURI);
        }
    }

 
    public function listModels() {
        $recordSet = R::getAll("SELECT modelURI, baseURI FROM models");
        if (!$recordSet)
            throw new DatabaseTDTException("Select failed");
        else {
            return $recordSet;
        }
    }

    public function modelExists($modelURI) {
        $param = Array(':modelURI' => $modelURI);
        $res = R::getRow("SELECT COUNT(*) cnt FROM models WHERE modelURI =:modelURI", $param);

        if (is_null($res))
            throw new DatabaseTDTException("Select failed");
        else {
            if (!$res['cnt']) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }


}

?>
