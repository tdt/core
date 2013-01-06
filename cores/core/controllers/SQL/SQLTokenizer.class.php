<?php
/**
 * Splits a SQL query string in tokens.
 *
 * @package The-Datatank/controllers/SQL
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class SQLTokenizer{
    private $tokens;
    private $queueindex = 0;

    public function getTokens(){
        return $this->tokens;
    }
    
    /* parsing: special chars */
    private static $specialchars=array('\n', '\r', ' ', '\t', 
        ')', '(', '+', '-', '*', '/', ',', '=', '<', '>', '!');
    private static $whitespacechars=array('\n', '\r', ' ', '\t');
    
    /* parsing: tool-functions 
       - read a char or 
       - check the kind of char 
       - check if there are still chars
       */
    static function readCharOrFail($querystring, &$index){
        if(SQLTokenizer::hasMoreChars($querystring, $index)){
            $index++;
            return $querystring[$index-1];
        }else{
            throw new Exception('SQL Tokenizer: Unexcepted end of query.');
        }
    }
    
    /**
     * Some functions used when reading...
     */
    static function hasMoreChars($querystring, $index){
        return $index < strlen($querystring);
    }

    static function isSpecialChar($char){
        return in_array($char, SQLTokenizer::$specialchars);
    }

    static function isWhiteSpaceChar($char){
        return in_array($char, SQLTokenizer::$whitespacechars);
    }
    
    /* parsing: 
        - reads next token (or whitespace character)
    */
    static function stringTillNextSpecialSymbol($querystring, &$index){
        $char=SQLTokenizer::readCharOrFail($querystring, $index);//read at least one char
        $str="";
        if($char=="'"){// string constant
            $str=$str.$char;
            $char=SQLTokenizer::readCharOrFail($querystring, $index);
            
            $escaped=false;
            while($escaped || $char!="'"){
                $escaped=false;
                $str=$str.$char;
                $char=SQLTokenizer::readCharOrFail($querystring, $index);
                if($char=='\\'){
                    $escaped=true;
                    $char=SQLTokenizer::readCharOrFail($querystring, $index);
                }
            }
            $str=$str.$char;
        }else{//all other tokens / whitespace
            $str=$str.$char;
            $readedToMuch=false;
            while(!SQLTokenizer::isSpecialChar($char) && SQLTokenizer::hasMoreChars($querystring, $index)){
                $char=SQLTokenizer::readCharOrFail($querystring, $index);
                if(!SQLTokenizer::isSpecialChar($char)){
                    $str=$str.$char;
                }else{
                    $readedToMuch=true;
                }
            }
            if($readedToMuch){
                $index--;//read the last special character another time
            }
        }
        
        return $str;
    }
    

    public function __construct($querystring){
        $this->tokens = array();
        
        $querystring = str_replace("\n", " ", $querystring);
        $querystring = str_replace("\r", " ", $querystring);
        $querystring = str_replace("\t", " ", $querystring);

        //how far are we in the querystring?
        $i = 0;
        
        //while not at the end of the string
        while($i < strlen($querystring)){
            //find string till next space or special symbol
            while(SQLTokenizer::hasMoreChars($querystring, $i)){
                $newToken = SQLTokenizer::stringTillNextSpecialSymbol($querystring, $i);
                if(!SQLTokenizer::isWhiteSpaceChar($newToken)){//is it a whitespace or a token?
                    $this->tokens[] = $newToken;
                }
            }
        }
        //var_dump($this->tokens);
    }
    
    public function hasNext(){
        return $this->queueindex < sizeof($this->tokens);
    }
    
    public function pop(){
        $this->queueindex++;
        return $this->tokens[$this->queueindex-1];
    }
    
    public function peek(){
        return $this->tokens[$this->queueindex];
    }

};

