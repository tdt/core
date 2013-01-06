<?php

/**
 * Extension of the DbModel Class from RAP API to support Redbean
 * 
 * @package The-Datatank/model/semantics
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */
class RbModel extends DbModel {

    public function RbModel($modelURI, $modelID, $baseURI = NULL) {
        parent::DbModel(NULL, $modelURI, $modelID, $baseURI);
    }

    protected function _checkNamespace($nmsp) {
        $res = true;
        $param = array(':modelID' => $this->modelID, ':namespace' => $nmsp);
        $sql = "SELECT * FROM namespaces
          	 WHERE modelID = :modelID AND namespace=:namespace";
        $rs = R::getAll($sql, $param);

        if (is_null($rs)) {
            throw new DatabaseTDTException('Select not performed');
        } else {
            if ($rs == false)
                $res = false;
        }
        return $res;
    }

    protected function _containsRow($row) {
        $param = array(
            ':modelID' => $this->modelID,
            ':subject' => $row[0],
            ':predicate' => $row[1],
            ':object' => $row[2],
            ':l_language' => $row[3],
            ':l_datatype' => $row[4],
            ':subject_is' => $row[5],
            ':object_is' => $row[6]
        );

        $sql = "SELECT modelID FROM statements
           WHERE modelID = :modelID
           AND subject =:subject
           AND predicate =:predicate
           AND object =:object
           AND l_language=:l_language
           AND l_datatype=:l_datatype
           AND subject_is=:subject_is
           AND object_is=:object_is";

        $res = R::getRow($sql, $param);

        if (!$res)
            return FALSE;
        return TRUE;
    }

    protected function _convertRecordSetToMemModel(&$recordSet) {
        $res = new MemModel($this->baseURI);
        //fields: subject, predicate, object, l_language, l_datatype, subject_is, object_is
        foreach ($recordSet as $record) {

            // subject
            if ($record['subject_is'] == 'r')
                $sub = new Resource($record['subject']);
            else
                $sub = new BlankNode($record['subject']);

            // predicate
            $pred = new Resource($record['predicate']);

            // object
            if ($record['object_is'] == 'r')
                $obj = new Resource($record['object']);
            elseif ($record['object_is'] == 'b')
                $obj = new BlankNode($record['object']);
            else {
                $obj = new Literal($record['object'], $record['l_language']);
                if ($record['l_datatype'])
                    $obj->setDatatype($record['l_datatype']);
            }

            $statement = new Statement($sub, $pred, $obj);
            $res->add($statement);
        }
        //Added by Miel Vander Sande, tranfer namespaces to result
        $res->addParsedNamespaces($this->getParsedNamespaces());
        return $res;
    }

    protected function _createDynSqlPart_SPO_param($subject, $predicate, $object) {
        $subject_is = is_a($subject, 'BlankNode') ? 'b' : (is_a($subject, 'Resource') ? 'r' : 'l');

        if ($subject != NULL) {
            $param[':subjLabel'] = $subject->getLabel();
            $param[':subjectIs'] = $subject_is;
        }
        if ($predicate != NULL)
            $param[':predLabel'] = $predicate->getLabel();
        if ($object != NULL) {
            $object_is = is_a($object, 'BlankNode') ? 'b' : (is_a($object, 'Resource') ? 'r' : 'l');
            $param[':objLabel'] = $object->getLabel();
            $param[':objectIs'] = $object_is;
            if (!is_a($object, 'Resource')) {
                if (!is_null($object->getLanguage()))
                    $param[':lLanguage'] = $object->getLanguage();

                $param[':lDatatype'] = $object->getDataType();
            }
        }

        return $param;
    }

    protected function _createDynSqlPart_SPO($subject, $predicate, $object) {
        // conditions derived from the parameters passed to the function
        $sql = '';
        if ($subject != NULL)
            $sql .= " AND subject=:subjLabel AND subject_is=:subjectIs";
        if ($predicate != NULL)
            $sql .= " AND predicate=:predLabel";
        if ($object != NULL) {
            if (is_a($object, 'Resource'))
                $sql .= " AND object=:objLabel AND object_is =:objectIs";
            else if (is_null($object->getLanguage()))
                $sql .= " AND object=:objLabel AND l_datatype=:lDatatype AND object_is =:objectIs";
            else
                $sql .= " AND object=:objLabel AND l_language=:lLanguage AND l_datatype=:lDatatype AND object_is =:objectIs";
        }
        return $sql;
    }

    protected function _getRecordSet(&$dbModel) {
        $param = array(':modelID' => $this->modelID);
        $sql = 'SELECT subject, predicate, object, l_language, l_datatype, subject_is, object_is
           FROM statements
           WHERE modelID = :modelID';

        return $recordSet = R::getAll($sql, $param);
    }

    public function add(&$statement) {
        if (!is_a($statement, 'Statement')) {
            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: add): Statement expected.';
            trigger_error($errmsg, E_USER_ERROR);
        }


        if (!$this->contains($statement)) {

            $subject_is = $this->_getNodeFlag($statement->getSubject());

            $param = array(':modelID' => $this->modelID, ':labelSubject' => $statement->getLabelSubject(), ':labelPredicate' => $statement->getLabelPredicate());
            $sql = 'INSERT INTO statements (modelID, subject, predicate, object, l_language, l_datatype, subject_is, object_is)
			        VALUES (:modelID, :labelSubject, :labelPredicate,';

            $param[':objLabel'] = $statement->getObject()->getLabel();
            $param[':subjectIs'] = $subject_is;

            if (is_a($statement->getObject(), 'Literal')) {
                $param[':objLanguage'] = $statement->getObject()->getLanguage();
                $param[':objDatatype'] = $statement->getObject()->getDatatype();
                $param[':l'] = 'l';

                $sql .= " :objLabel, :objLanguage, :objDatatype, :subjectIs, :l)";
            } else {
                $param[':objectIs'] = $this->_getNodeFlag($statement->getObject());

                $sql .= ":objLabel,'','',:subjectIs,:objectIs)";
            }

            $rs = R::exec($sql, $param);

            if (!$rs) {
                throw new DatabaseTDTException('Insert not performed');
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function addModel(&$model) {
        if (!is_a($model, 'Model')) {
            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: addModel): Model expected.';
            trigger_error($errmsg, E_USER_ERROR);
        }

        $blankNodes_tmp = array();

        if (is_a($model, 'MemModel')) {
            R::begin();
            foreach ($model->triples as $statement)
                $this->_addStatementFromAnotherModel($statement, $blankNodes_tmp);
            $this->addParsedNamespaces($model->getParsedNamespaces());
            R::commit();
        } elseif (is_a($model, 'RbModel')) {
            R::begin();
            $memModel = $model->getMemModel();
            foreach ($memModel->triples as $statement)
                $this->_addStatementFromAnotherModel($statement, $blankNodes_tmp);
            $this->addParsedNamespaces($model->getParsedNamespaces());
            R::commit();
        }
    }

    public function addNamespace($prefix, $nmsp) {

        if ($nmsp != '' && $prefix != '') {
            $param = array(':prefix' => $prefix, ':modelID' => $this->modelID, ':namespace' => $nmsp);
            if ($this->_checkNamespace($nmsp)) {
                $sql = "UPDATE namespaces SET prefix=:prefix WHERE modelID=:modelID AND namespace=:namespace";
            } else {
                $sql = "INSERT INTO namespaces (modelID, namespace, prefix) VALUES (:modelID, :namespace, :prefix)";
            }
            $rs = R::exec($sql, $param);

            //if (!$rs)
            if (is_null($rs))
                throw new DatabaseTDTException('Namespace not added or updated in Database');
        }
    }

    public function contains(&$statement) {
        $param = array(':modelID' => $this->modelID);
        $sql = 'SELECT modelID FROM statements WHERE modelID =:modelID ';

        $param = array_merge($param, $this->_createDynSqlPart_SPO_param($statement->getSubject(), $statement->getPredicate(), $statement->getObject()));
        $sql .= $this->_createDynSqlPart_SPO($statement->getSubject(), $statement->getPredicate(), $statement->getObject());

        $res = R::getRow($sql, $param);
        if (!$res) {
            return FALSE;
        }
        return TRUE;
    }

    public function containsAll(&$model) {
        if (is_a($model, 'MemModel')) {

            foreach ($model->triples as $statement)
                if (!$this->contains($statement))
                    return FALSE;
            return TRUE;
        }

        elseif (is_a($model, 'RbModel')) {

            $recordSet = $this->_getRecordSet($model);
            while (!$recordSet->EOF) {
                if (!$this->_containsRow($recordSet->fields))
                    return FALSE;
                $recordSet->moveNext();
            }
            return TRUE;
        }

        $errmsg = RDFAPI_ERROR . '(class: RbModel; method: containsAll): Model expected.';
        trigger_error($errmsg, E_USER_ERROR);
    }

    public function containsAny(&$model) {
        if (is_a($model, 'MemModel')) {

            foreach ($model->triples as $statement)
                if ($this->contains($statement))
                    return TRUE;
            return FALSE;
        }

        elseif (is_a($model, 'DbModel')) {

            $recordSet = $this->_getRecordSet($model);
            while (!$recordSet->EOF) {
                if ($this->_containsRow($recordSet->fields))
                    return TRUE;
                $recordSet->moveNext();
            }
            return FALSE;
        }

        $errmsg = RDFAPI_ERROR . '(class: RbModel; method: containsAny): Model expected.';
        trigger_error($errmsg, E_USER_ERROR);
    }

    public function delete() {
        $param = array(':modelID' => $this->modelID);

        R::begin();

        $del1 = R::exec('DELETE FROM models WHERE modelID=:modelID', $param);
        $del2 = R::exec('DELETE FROM statements WHERE modelID=:modelID', $param);
        $del3 = R::exec('DELETE FROM namespaces WHERE modelID=:modelID', $param);

        if (del1 != null && $del2 != null && $del3 != null)
            R::commit();
        else {
            R::rollback();
            throw new DatabaseTDTException('Delete transaction not performed');
        }
    }

    public function find($subject, $predicate, $object) {
        if ((!is_a($subject, 'Resource') && $subject != NULL) ||
                (!is_a($predicate, 'Resource') && $predicate != NULL) ||
                (!is_a($object, 'Node') && $object != NULL)) {

            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: find): Parameters must be subclasses of Node or NULL';
            trigger_error($errmsg, E_USER_ERROR);
        }

        $param = array(':modelID' => $this->modelID);
        // static part of the sql statement
        $sql = 'SELECT subject, predicate, object, l_language, l_datatype, subject_is, object_is
           FROM statements
           WHERE modelID = :modelID';

        // dynamic part of the sql statement
        $param = array_merge($param, $this->_createDynSqlPart_SPO_param($subject, $predicate, $object));
        $sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

        // execute the query
        $recordSet = R::getAll($sql, $param);

        if (!is_array($recordSet))
            throw new DatabaseTDTException('Select for finding statement failed');

        // write the recordSet into memory Model
        else
            return $this->_convertRecordSetToMemModel($recordSet);
    }

    public function findCount($subject, $predicate, $object) {

        if ((!is_a($subject, 'Resource') && $subject != NULL) ||
                (!is_a($predicate, 'Resource') && $predicate != NULL) ||
                (!is_a($object, 'Node') && $object != NULL)) {

            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: find): Parameters must be subclasses of Node or NULL';
            trigger_error($errmsg, E_USER_ERROR);
        }

        $param = array(':modelID' => $this->modelID);
        // static part of the sql statement
        $sql = 'SELECT COUNT(*) FROM statements WHERE modelID = :modelID';

        // dynamic part of the sql statement
        $param = array_merge($param, $this->_createDynSqlPart_SPO_param($subject, $predicate, $object));
        $sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

        // execute the query
        $recordSet = R::getAll($sql, $param);

        if (!$recordSet)
            throw new DatabaseTDTException('Select not performed');
        else
            return $recordSet->fields[0];
    }

    public function findFirstMatchingStatement($subject, $predicate, $object, $offset=-1) {
        if ((!is_a($subject, 'Resource') && $subject != NULL) ||
                (!is_a($predicate, 'Resource') && $predicate != NULL) ||
                (!is_a($object, 'Node') && $object != NULL)) {

            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: find): Parameters must be subclasses of Node or NULL';
            trigger_error($errmsg, E_USER_ERROR);
        }

        $param = array(':modelID' => $this->modelID);
        // static part of the sql statement
        $sql = 'SELECT subject, predicate, object, l_language, l_datatype, subject_is, object_is
           FROM statements WHERE modelID = :modelID';

        // dynamic part of the sql statement
        $param = array_merge($param, $this->_createDynSqlPart_SPO_param($subject, $predicate, $object));
        $sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

        // execute the query
        //$recordSet = & $this->dbConn->selectLimit($sql, 1, ($offset));

        $recordSet = R::getAll($sql, $param);

        if (is_array($recordSet)) {
            if (count($recordSet) > 0) {
                if ($offset >= count($recordSet))
                    throw new DatabaseTDTException('Number of rows in result is not correct');
                $recordSet = array($recordSet[($offset + 1)]);
            }
        }

        if (!is_array($recordSet))
            throw new DatabaseTDTException('Result is not an array');
        else {
            if (count($recordSet) <= 0)
                return NULL;
            else {
                $memModel = $this->_convertRecordSetToMemModel($recordSet);
                $triples = $memModel->triples;
                return $triples[0];
            }
        }
    }

    public function findVocabulary($vocabulary) {

        $param = array(':modelID' => $this->modelID, ':voc' => $vocabulary . '%');

        $sql = "SELECT subject, predicate, object, l_language, l_datatype, subject_is, object_is
           FROM statements WHERE modelID = :modelID AND predicate LIKE :voc";

        $recordSet = R::getAll($sql, $param);

        if (is_null($recordSet))
            throw new DatabaseTDTException('Select not performed');

        // write the recordSet into memory Model
        else
            return $this->_convertRecordSetToMemModel($recordSet);
    }

    /*
     * New function added by Miel Vander Sande for finding wildcarded statements
     */

    public function findWildcarded($subject_wc, $predicate_wc, $object_wc) {
        if ((!is_string($subject_wc) && $subject_wc != NULL) ||
                (!is_string($predicate_wc) && $predicate_wc != NULL) ||
                (!is_string($object_wc) && $object_wc != NULL)) {

            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: findWildcarded): Parameters must be string or NULL';
            trigger_error($errmsg, E_USER_ERROR);
        }

        $param = array(':modelID' => $this->modelID);
        // static part of the sql statement
        $sql = 'SELECT subject, predicate, object, l_language, l_datatype, subject_is, object_is
           FROM statements
           WHERE modelID = :modelID';

        if ($subject_wc != NULL) {
            $param[':subjLabel'] = $subject_wc;
            $sql .= " AND subject LIKE :subjLabel";
        }
        if ($predicate_wc != NULL) {
            $param[':predLabel'] = $predicate_wc;
            $sql .= " AND predicate LIKE :predLabel";
        }
        if ($object_wc != NULL) {
            $param[':objLabel'] = $object_wc;
            $sql .= " AND object LIKE :objLabel";
        }

        // execute the query
        $recordSet = R::getAll($sql, $param);

        if (!is_array($recordSet))
            throw new DatabaseTDTException('Select for finding statement failed');

        // write the recordSet into memory Model
        else
            return $this->_convertRecordSetToMemModel($recordSet);
    }

    public function getParsedNamespaces() {
        $param = array(':modelID' => $this->modelID);
        $sql = "SELECT modelID, namespace, prefix FROM namespaces WHERE modelID =:modelID";
        $temp = false;
        $res = R::getAll($sql, $param);
        if ($res) {
            foreach ($res as $record) {
                $temp[$record['namespace']] = $record['prefix'];
            }
        }
        return $temp;
    }

    public function rdqlQuery($queryString, $returnNodes = TRUE) {
        return parent::rdqlQuery($queryString, $returnNodes);
    }

    public function remove(&$statement) {
        if (!is_a($statement, 'Statement')) {
            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: remove): Statement expected.';
            trigger_error($errmsg, E_USER_ERROR);
        }
        $param = array(':modelID' => $this->modelID);
        $sql = 'DELETE FROM statements WHERE modelID=:modelID';
        $param = array_merge($param, $this->_createDynSqlPart_SPO_param($statement->getSubject(), $statement->getPredicate(), $statement->getObject()));
        $sql .= $this->_createDynSqlPart_SPO($statement->getSubject(), $statement->getPredicate(), $statement->getObject());

        $rs = R::exec($sql, $param);

        return $rs;
    }

    public function removeNamespace($nmsp) {
        $param = array(':modelID' => $this->modelID, ':namespace' => $nmsp);
        $sql = 'DELETE FROM namespaces WHERE modelID=:modelID AND namespace=:namespace';

        $rs = R::exec($sql, $param);
        if (!$rs)
            throw new DatabaseTDTException('Delete not performed');
        else {
            return true;
        }
    }

    public function replace($subject, $predicate, $object, $replacement) {
        // check the correctness of the passed parameters
        if (((!is_a($subject, 'Resource') && $subject != NULL) ||
                (!is_a($predicate, 'Resource') && $predicate != NULL) ||
                (!is_a($object, 'Node') && $object != NULL)) ||
                (($subject != NULL && is_a($replacement, 'Literal')) ||
                ($predicate != NULL && (is_a($replacement, 'Literal') ||
                is_a($replacement, 'BlankNode'))))) {
            $errmsg = RDFAPI_ERROR . '(class: RbModel; method: find): Parameter mismatch';
            trigger_error($errmsg, E_USER_ERROR);
        }

        if (!(!$subject && !$predicate && !$object)) {

            // create an update sql statement
            $comma = '';
            $sql = 'UPDATE statements
             SET ';

            $param = array();
            if ($subject) {
                $param[':subject'] = $replacement->getLabel();
                $param[':subject_is'] = $this->_getNodeFlag($replacement);

                $sql .= " subject =:subject, subject_is=:subject_is ";
                $comma = ',';
            }
            if ($predicate) {
                $param[':predicate'] = $replacement->getLabel();
                $sql .= $comma . " predicate=:predicate ";
                $comma = ',';
            }
            if ($object) {
                $param[':object'] = $replacement->getLabel();
                $param[':object_is'] = $this->_getNodeFlag($replacement);
                $sql .= $comma . ' object=:object, object_is=:object_is ';
                if (is_a($replacement, 'Literal')) {
                    $param[':l_language'] = $replacement->getLanguage();
                    $param[':l_datatype'] = $replacement->getDataType();
                    $sql .= ", l_language=:l_language "
                            . ", l_datatype=:l_datatype ";
                }
            }
            $param[':modelID'] = $this->modelID;
            $sql .= 'WHERE modelID =:modelID';
            $param = array_merge($param, $this->_createDynSqlPart_SPO_param($subject, $predicate, $object));
            $sql .= $this->_createDynSqlPart_SPO($subject, $predicate, $object);

            // execute the query
            $rs = R::exec($sql, $param);

            if (!$rs)
                throw new DatabaseTDTException('Replace update not found');
        }
    }

    public function setBaseURI($uri) {
        $this->baseURI = $this->_checkBaseURI($uri);

        $param = array(':baseURI' => $this->baseURI, ':modelID' => $this->modelID);
        $rs = R::exec('UPDATE models SET baseURI=:baseURI WHERE modelID=:modelID', $param);

        if (is_null($rs))
            throw new DatabaseTDTException('Update not performed');
    }

    public function size() {

        $param = array(':modelID' => $this->modelID);
        $count = R::getRow('SELECT COUNT(modelID) cnt FROM statements WHERE modelID = :modelID', $param);
        return $count['cnt'];
    }

    public function toString() {
        return 'RbModel[modelURI=' . $this->modelURI . '; baseURI=' . $this->getBaseURI() . ';  size=' . $this->size() . ']';
    }

}

?>
