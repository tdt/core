<?php
/**
 * Parser a SQL query and generates a tree of filters. 
 * It throw SQLParseTDTExceptions (TODO)
 *
 * @package The-Datatank/controllers/SQL
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
include_once("lib/parse_engine.php");
include_once("controllers/SQL/SQLGrammarFunctions.php");
include_once("controllers/SQL/SQLTokenizer.class.php");
include_once("controllers/spectql/parseexceptions.php");
include_once("controllers/SQL/SQLgrammar.php");


class SQLParser{
    private $querystring;

    /**
     * The "keywords" (things that are not possible for identifiers)
     */ 
    private static $keywords = array(
        "SELECT", "FROM", "DISTINCT", "GROUP", "BY", "WHERE", "HAVING", "LIKE", "UNION", "BETWEEN", "INNER", "LEFT", "RIGHT", "FULL", "JOIN", "IN",
        "+", "-", "*", "/", "=", "<", ">", "OR", "AND", "AS",
        ",", "(", ")", "!", "ALL", "ANY", "|", "EXTRACT", "LIMIT", "OFFSET",
        "SECOND", "MINUTE", "HOUR", "DAY", "WEEK", "MONTH", "YEAR", "MINUTE_SECOND", "HOUR_SECOND", "HOUR_MINUTE", "DAY_SECOND", "DAY_MINUTE", "DAY_HOUR", "YEAR_MONTH",
        "INTERVAL", "DATE_ADD", "DATE_SUB",
        "ORDER", "ASC", "DESC");
    
    /**
     * Takes: A SQL Query
     * Returns: A uniform representation of the filter as a tree
     */
    public function __construct($querystring) {
        //url decode
        $this->querystring = ltrim(urldecode($querystring),"/");
    }

    public function interpret(){
        $querystring= $this->querystring;
        $tokenizer = new SQLTokenizer($querystring);       
        $this->parser = new parse_engine(new SQLgrammar());

        if (!strlen($querystring)){
            //give an error, but in javascript, redirect to our index.html
            header("HTTP1.1 491 No SQL Query given");
            echo "<script>window.location = \"index.html\";</script>";
            exit(0);
            
        }
        //echo "-<br/>";
        try {
            while($tokenizer->hasNext()){
                $t = $tokenizer->pop();
                //echo "'".$t."'<br/>";
                
                $nextIsNumericConstant = false;
                if($tokenizer->hasNext()){
                    $nexttoken = $tokenizer->peek();
                    $nextIsNumericConstant = is_numeric($nexttoken);
                }
                
                if ($t=="-" && $nextIsNumericConstant){
                    //echo " --> constant <br/>";
                    $t = $tokenizer.pop();
                    $this->parser->eat('constant', "-".$t);
                }else if ($this->is_constant($t)){
                    //echo " --> constant <br/>";
                    $this->parser->eat('constant', $this->getStripedConstant($t));
                }
                else if (!$this->is_keyword($t)){
                    //echo " --> identifier <br/>";
                    $this->parser->eat('name', $t);
                }
                else{
                    //echo " --> keyword <br/>";
                    if(strlen($t)==1){
                        $this->parser->eat("'".strtoupper($t)."'", null);
                    }else{
                        $this->parser->eat(strtoupper($t), null);
                    }
                }
            }
            $tree =  $this->parser->eat_eof();
            return $tree;
        } catch (parse_error $e) {
            //throw new Exception($e->getMessage());//TODO?
            throw $e;
        }
    }
    
    private function is_constant(&$token){
        if($token[0]=="'"){
            return true;//string
        }else if(strtoupper($token)=="TRUE"){
            $token="true";
            return true;//boolean: true
        }else if(strtoupper($token)=="FALSE"){
            $token="false";
            return true;//boolean: false
        }else if(is_numeric($token)){
            return true;//numeric
        }
        return false;
    }
    
    private function is_keyword($token){
        return in_array(strtoupper($token), SQLParser::$keywords);
    }
    
    private function getStripedConstant($const){
        if(substr($const,0,1)=="'"){
            $const = substr($const,1,-1);
        }
        return $const;
    }
}
