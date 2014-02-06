<?php

/**
*   Class with Sparql-Unittest helper methods
*/
class SparqlTestHelper
{
    /**
    *   Compares calculated with expected result.
    *   Values can be at any position and do not need to be ordered.
    *
    *   @param array $table  Result calculated by the engine
    *   @param array $result Expected result
    *
    *   @return boolean True if both arrays contain the same values
    */
    public static function resultCheck($table, $result)
    {
        if (!is_array($table) && !is_array($result)) {
            return $table === $result;
        }

        $match    = 0;
        $rows     = 0;
        if (isset($result['rowcount'])) {
            $rowcount = $result['rowcount'];
            $hits     = $result['hits'];
            $result   = $result['part'];
        } else {
            //e.g. dawg2 tests pass a "table" as $result
            $rowcount = count($result);
            $hits     = count($result);
        }

        if ($rowcount == 0 && ($table == 'false' || $table == array())) {
            return true;
        }
        if (count($table) != count($result)) {
            return false;
        }


        if (!is_array($table)) {
            return false;
        }

        foreach ($table as $key => $value){
            foreach ($result as $innerKey => $innervalue){
                $match = 0;
                foreach ($innervalue as $varname => $varval){
                    if (isset($value[$varname])){
                        if (gettype($value[$varname]) == gettype($varval)
                            && $value[$varname] == $varval
                        ) {
                            $match++;
                        } else {
                            break;
                        }
                    }
                    if ($match == $rowcount){
                        $rows++;
                        unset($result[$innerKey]);
                        break;
                    }
                }
            }
        }

        if ($hits == $rows) {
            return true;
        } else {
            return false;
        }
    }//public static function resultCheck($table, $result)



    /**
    *   Compares calculated with expected result.
    *   Values need to be in the exact same order.
    *
    *   @param array $table  Result calculated by the engine
    *   @param array $result Expected result
    *
    *   @return boolean True if both arrays contain the same values
    */
    public static function resultCheckSort($table, $result)
    {
        if (count($result) != count($table)) {
            return false;
        }

        foreach ($result as $key => $value) {
            foreach ($value as $varname => $varvalue) {
                if ($varvalue != $table[$key][$varname]) {
                    return false;
                }
            }
        }
        return true;
    }//public static resultCheckSort($table, $result)



    /**
    *   Helper method that creates a sparql filter string from the
    *   given filter tree.
    */
    static function renderTree($tree)
    {
        if (!is_array($tree) || !isset($tree['type'])) {
            return 'Parser is broken';
        }
        $negation = isset($tree['negated']) ? '!' : '';
        switch ($tree['type']) {
            case 'equation':
                return $negation . '(' . self::renderTree($tree['operand1'])
                    . ' ' . $tree['operator'] . ' '
                    . self::renderTree($tree['operand2']) . ')';
            case 'value':
                return $negation . $tree['value'];
            case 'function':
                return $negation . $tree['name'] . '('
                    . implode(
                        ', ',
                        array_map(
                            array('self', 'renderTree'),
                            $tree['parameter']
                        )
                    ) . ')';
            default:
                var_dump($tree);
                throw new Exception('Unsupported tree type: ' . $tree['type']);
                break;
        }
    }//static function renderTree($tree)



    /**
    *   renders the result part of a query object as a string
    */
    static function renderResult(Query $query)
    {
        $s = '';
        foreach ($query->getResultPart() as $gp) {
            //dumpGp:
            $s .= 'GP #' . $gp->getId();
            if ($gp->getOptional() !== null)     $s .= ' optionalTo('   . $gp->getOptional() . ')';
            if ($gp->getUnion() !== null)        $s .= ' unionWith('    . $gp->getUnion() . ')';
            if ($gp->getSubpatternOf() !== null) $s .= ' subpatternOf(' . $gp->getSubpatternOf() . ')';
            if (count($gp->getConstraints()) > 0)$s .= ' filter(' . count($gp->getConstraints()) . ')';
            $s .= "\n";
            if ($gp->getTriplePatterns()) {
                foreach ($gp->getTriplePatterns() as $tp) {
                    $s .= '  ' . $tp->getSubject() . ', ' . $tp->getPredicate()
                        . ', ' . $tp->getObject() . "\n";
                }
            }
        }
        return $s;
    }//static function renderResult($query)



    /**
    *   Converts a MemModel into a query result array.
    *   Required for the DAWG test cases.
    *
    *   @param Model $model Model object to extract data from
    *   @return array Result array
    *
    *   @see http://www.w3.org/2001/sw/DataAccess/tests/README
    *   @see http://www.w3.org/2001/sw/DataAccess/tests/result-set.n3
    */
    static function convertModelToResultArray($model)
    {
        $graphset = ModelFactory::getDatasetMem('Dataset1');
        $graphset->setDefaultGraph($model);
        $parser   = new SparqlParser();
        $engine   = SparqlEngine::factory($model);

        $strSparqlQuery = '
            PREFIX rs: <http://www.w3.org/2001/sw/DataAccess/tests/result-set#>
            SELECT ?varname WHERE { ?x rs:resultVariable ?varname }
        ';
        $q         = $parser->parse($strSparqlQuery);
        $variables = $engine->queryModel($graphset, $q, false);

        $arVars             = array();
        $strSparqlQueryPart = '';
        $nCount             = 0;
        foreach ($variables as $var) {
            $varname  = '?' . $var['?varname']->label;
            $name     = substr($varname, 1);
            $arVars[] = $varname;
            $strSparqlQueryPart .=
               ' ?thing rs:binding ?binding' . $nCount . '.
                 ?binding' . $nCount . ' rs:value ' . $varname . '.
                 ?binding' . $nCount . ' rs:variable "' . $name . '".';
            ++$nCount;
        }

        $strSparqlQuery = '
            PREFIX rs: <http://www.w3.org/2001/sw/DataAccess/tests/result-set#>
            SELECT ' . implode($arVars, ' ') . '
            WHERE {
                ?some rs:solution ?thing.
               ' . $strSparqlQueryPart . '
            }
        ';
//echo $strSparqlQuery;
        $q        = $parser->parse($strSparqlQuery);
        $arResult = $engine->queryModel($graphset, $q, false);
//var_dump($arResult);
//die();

        return $arResult;
    }//static function convertModelToResultArray($model)

}//class SparqlTestHelper
?>