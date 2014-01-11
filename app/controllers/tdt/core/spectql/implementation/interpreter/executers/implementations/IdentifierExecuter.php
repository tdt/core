<?php

/**
 * Executes an Identifier
 *
 * format:
 * - package.package.resource
 * - package.package.resource.name_of_column
 * - alias.name_of_column
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\data\UniversalFilterTableContent;
use tdt\core\spectql\implementation\data\UniversalFilterTableContentRow;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeader;
use tdt\core\spectql\implementation\interpreter\Environment;
use tdt\core\spectql\implementation\interpreter\executers\base\AbstractUniversalFilterNodeExecuter;
use tdt\core\spectql\implementation\interpreter\IInterpreterControl;
use tdt\core\spectql\implementation\interpreter\sourceusage\SourceUsageData;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

class IdentifierExecuter extends AbstractUniversalFilterNodeExecuter {

    private $interpreter;
    private $topenv;
    private $header;
    private $singlevaluecolumnheader;
    private $singlevalueindex;
    private $isColumn;
    private $isNewTable;

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {

        $this->filter = $filter;
        $this->interpreter = $interpreter;
        $this->topenv = $topenv;

        if ($preferColumn) {
            $this->isNewTable = false;

            $this->isColumn = true;

            $column_ids = explode('.', $this->filter->getIdentifierString());

            $this->header = $this->getColumnDataHeader($topenv, $column_ids[0]);
            if ($this->header === null) {
                \App::abort(500, "The identifier " . $this->filter->getIdentifierString() . " cannot be found - it's not a column.");
            }

            if (!$this->isColumn) {
                $this->singlevaluecolumnheader = $this->header->getColumnInformationById($this->header->getColumnId());
                $this->header = new UniversalFilterTableHeader(array($this->singlevaluecolumnheader->cloneColumnNewId()), true, true);
            }
        } else {
            $this->isNewTable = true;
            // load new table
            $tableName = $filter->getIdentifierString();
            try {
                $this->header = $interpreter->getTableManager()->getTableHeader($tableName);
            } catch (Exception $e) {
                \App::abort(500, "The identifier $tableName cannot be found.");
            }
        }
    }

    public function getExpressionHeader() {
        return $this->header;
    }

    public function evaluateAsExpression() {

        if ($this->isNewTable) {

            $tableName = $this->filter->getIdentifierString();
            return $this->interpreter->getTableManager()->getTableContent($tableName, $this->header);
        } else {

            if (!$this->isColumn) {

                $newRow = new UniversalFilterTableContentRow();
                $value = $this->topenv->getSingleValue($this->singlevalueindex)->copyValueTo($newRow, $this->singlevaluecolumnheader->getId(), $this->header->getColumnId());

                $content = new UniversalFilterTableContent();
                $content->addRow($newRow);

                return $content;
            } else {

                $column_ids = explode('.', $this->filter->getIdentifierString());
                return $this->getColumnDataContent($this->topenv->getTable(), $this->filter->getIdentifierString(), $this->header);
            }
        }
    }

    /**
     * Get a single column from the data (header)
     *
     * @return UniversalFilterTableHeader
     */
    private function getColumnDataHeader(Environment $topenv, $fullid) {

        if ($fullid == "*") {
            //special case => current table
            return $topenv->getTable()->getHeader()->cloneHeader();
        }

        $originalheader = $topenv->getTable()->getHeader();
        $columnid = $originalheader->getColumnIdByName($fullid);

        if ($columnid === null) {

            $this->isColumn = false; //it's a single value...

            $foundheader = null;

            for ($index = 0; $index < $topenv->getSingleValueCount(); $index++) {
                $columninfo = $topenv->getSingleValueHeader($index);

                if ($columninfo->matchName(explode(".", $fullid))) {
                    if ($foundheader != null) {
                        throw new Exception("Ambiguous identifier: " . $fullid . ". Please use aliases to remove the ambiguity."); //can only occured in nested queries or joins
                    }
                    $this->singlevalueindex = $index;
                    $foundheader = new UniversalFilterTableHeader(array($columninfo), true, true);
                }
            }
            return $foundheader; //if null: identifier not found
        } else {
            //check single values for another match (to give an exception)
            for ($index = 0; $index < $topenv->getSingleValueCount(); $index++) {
                if ($topenv->getSingleValueHeader($index)->matchName(explode(".", $fullid))) {
                    throw new Exception("Ambiguos identifier: " . $fullid . ". Please use aliases to remove the ambiguity."); //can only occured in nested queries or joins
                }
            }

            //return
            $newHeaderColumn = $originalheader->getColumnInformationById($columnid)->cloneColumnNewId();

            $columnHeader = new UniversalFilterTableHeader(array($newHeaderColumn), $originalheader->isSingleRowByConstruction(), true);

            return $columnHeader;
        }
    }

    /**
     * Get a column from the data (content)
     *
     * @param UniversalFilterTableHeader $header
     * @return UniversalFilterTableContent
     */
    private function getColumnDataContent($table, $fullid, $header) {

        $content = $table->getContent();

        if ($fullid == "*") {

            $contentCopy = new UniversalFilterTableContent();

            for ($rowindex = 0; $rowindex < $content->getRowCount(); $rowindex++) {
                $contentCopy->addRow($content->getRow($rowindex));
            }

            return $contentCopy;
        }

        $oldheader = $table->getHeader();

        $column_ids = explode('.', $fullid);

        $oldcolumnid = $oldheader->getColumnIdByName($column_ids[0]);
        $newcolumnid = $header->getColumnId();

        $newContent = new UniversalFilterTableContent();
        $rows = array();

        for ($index = 0; $index < $content->getRowCount(); $index++) {

            $oldRow = $content->getRow($index);

            // If a hierarhical column identifier is passed, identified with '.'
            // process the value of the column with the rest of the identifiers.
            // Note that each column is identified by the high level name e.g. "top"
            // If top.person.name is passed, then we get the value from the cell identified with "top"
            // and process the value with person.name, assuming that the path exists in the value of the cell.
            $newRow = new UniversalFilterTableContentRow();

            if(count($column_ids) > 1){

                // Remove the column identifier from the total identifier for it already identifies the entire object
                // now we just need the lower properties of the value.
                $copy_ids = $column_ids;
                array_shift($copy_ids);

                $old_value = $oldRow->getCellValue($oldcolumnid);
                $data = $this->applyFilter($old_value, $copy_ids);
                $newRow->defineValue($newcolumnid, $data);
            }else{
                $oldRow->copyValueTo($newRow, $oldcolumnid, $newcolumnid);
            }

            $newContent->addRow($newRow);
        }

        return $newContent;
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex) {

        if (!$this->isNewTable) {
            return array();
        } else {
            $sourceId = $this->interpreter->getTableManager()->getSourceIdFromIdentifier($this->filter->getIdentifierString());
            return array(new SourceUsageData($this->filter, $parentNode, $parentIndex, $sourceId));
        }
    }

    /**
     * Apply path filtering on the data
     * @return mixed filtered object
     */
    private static function applyFilter($data, $path){

        foreach($path as $property){

            if(is_object($data) && $key = self::propertyExists($data, $property)){
                $data = $data->$key;
            }elseif(is_array($data)){

                if($key = self::keyExists($data, $property)){
                    $data = $data[$key];
                }else if(is_numeric($property)){
                    for($i = 0; $i <= $property; $i++){
                        $result = array_shift($data);
                    }

                    $data = $result;
                }else{
                    return null;
                }
            }else{
                return null;
            }
        }

        return $data;
    }

    /**
     * Case insensitive search for a property of an object
     */
    private static function propertyExists($object, $property){

        $vars = get_object_vars($object);
        foreach($vars as $key => $value) {
            if(strtolower($property) == strtolower($key)) {
                return $key;
                break;
            }
        }
        return false;
    }

    /**
     * Case insensitive search for a key in an array
     */
    private static function keyExists($array, $property){

        foreach($array as $key => $value) {
            if(strtolower($property) == strtolower($key)) {
                return $key;
                break;
            }
        }
        return false;
    }

}
