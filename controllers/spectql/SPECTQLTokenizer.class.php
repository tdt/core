<?php
/**
 * Splits a query string in tokens. You can easily get the next, peak or pop the next token. As the query string will never take the size of a program, so we will prefer a nice architecture over performance.
 *
 * @package The-Datatank/controllers/spectql
 * @copyright (C) 2011 by OKFN chapter Belgium vzw/asbl
 * @license LGPL
 * @author Pieter Colpaert
 * @organisation Hogent
 */
class SPECTQLTokenizer{
    private $tokens;
    private $index = 0;

    public function getTokens(){
        return $this->tokens;
    }
    

    public function __construct($querystring, $symbols){
        $this->tokens = array();
        //We need to prioritize things. The first or the first 2 characters we read might already be a symbol, if it is, add it to the tokens array, if it isn't, enlarge the string until we've found a symbol again.

        //how far are we in the querystring?
        $i = 0;
        //first loop will select each character of the string. i is the start of a new token
        while($i < strlen($querystring)){
            //A temporal string which will end up in being a token
            $tempstr = "";
            //Terminates the token which will be stored
            $symbol = "";
            //itoken will be the index inside a certain token
            $itoken = $i;
            //new loop will continue while no symbol has been found
            //it also checks if a token exists when combining it with the next character or if in a group (between '')
            
            while($itoken < strlen($querystring) && !in_array(substr($querystring,$itoken, 1), $symbols) && !in_array(substr($querystring,$itoken,2),$symbols)){
                $tempstr .= substr($querystring,$itoken, 1);                
                $itoken ++;
            }
            
            //store the tempstr as a token if there's something in it (there isn't at start, and also not when a symbol is placed right after another
            if(strlen($tempstr) > 0){
                $this->tokens[] = $tempstr;
            }
            //now store the symbols in the tokens array as well, if there is one
            //first check whether 2 characters is a symbol!                        
            if(in_array(substr($querystring,$itoken,2),$symbols)){
                $symbol = substr($querystring,$itoken, 2);
            }else if(in_array(substr($querystring,$itoken, 1), $symbols)){
                $symbol = substr($querystring,$itoken, 1);
            }
            //check if group, then just skip all
            if($symbol == "'"){
                //shift 'zkhjbj... until end'
                $itoken++;
                $start = $itoken;
                while(substr($querystring,$itoken, 1) != "'" || substr($querystring,$itoken, 2) == "''" && $itoken < strlen($querystring)){
                    $itoken ++;
                }
                $symbol = substr($querystring,$start, $itoken-$start);
                //add another one for the other single quote
                if(substr($querystring,$itoken, 1) == "'"){
                    $itoken ++;
                }
                //add 2 single quotes 
                $this->tokens[] = "'";
                $this->tokens[] = $symbol;
                $this->tokens[] = "'";
                $i = $itoken;
            }else if($symbol != ""){
                $this->tokens[] = $symbol;
                //when a token is stored, give $i a new starting point. The new starting point lies behind the symbol that terminates the previous one
                $i = $itoken + strlen($symbol);
            }else{
                $i = $itoken + strlen($symbol);
            }
        }
        //DBG:var_dump($this->tokens);
    }
    
    public function hasNext(){
        return $this->index < sizeof($this->tokens);
    }
    
    public function pop(){
        $this->index++;
        return $this->tokens[$this->index-1];
    }
    
    public function peek(){
        return $this->tokens[$this->index];
    }

}

