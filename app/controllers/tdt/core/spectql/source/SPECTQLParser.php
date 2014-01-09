<?php

/**
 * Parser a SPECTQL query and generates a stack of expressions. It throw SPECTQLParseTDTExceptions
 *
 * @package The-Datatank/controllers/spectql
 * @copyright (C) 2011 by OKFN chapter Belgium vzw/asbl
 * @license LGPL
 * @author Pieter Colpaert
 * @organisation Hogent
 */

namespace tdt\core\spectql\source;

use \parse_engine;
use \spectql;

class SPECTQLParser {

    private $querystring;

    /**
     * Provides the link with the parser character
     */
    private static $symbols = array(
        "." => "'.'",
        ">" => "'>'",
        "<" => "'<'",
        "==" => "EQ",
        ">=" => "GE",
        "<=" => "LE",
        "!=" => "NE",
        ":=" => "ALIAS",
        "~" => "'~'",
        "(" => "'('",
        ")" => "')'",
        "{" => "'{'",
        "}" => "'}'",
        "=>" => "LN",
        "|" => "'|'",
        "&" => "'&'",
        "!" => "'!'",
        "+" => "'+'",
        "-" => "'-'",
        "/" => "'/'",
        "*" => "'*'",
        ":" => "':'",
        "," => "','",
        "^" => "'^'",
        "%" => "'%'",
        "?" => "'?'",
        "'" => "SQ"
    );

    /**
     * An $expression is a string containing all information after a /
     * For instance: http://datatank.demo.ibbt.be/spectql/Belgium{Zonenr,count(Zonenaam), avg(PostNr)}
     */
    public function __construct($querystring) {
        // url decode
        // http://php.net/manual/en/function.urldecode.php
        // we use a + sign to use order functionality, but decode will translate
        // a + to a whitespace, so lets first translate the + sign to it's urlencoding (%2B)
        $querystring = str_replace('+', '%2B', $querystring);

        $this->querystring = ltrim(urldecode($querystring), "/");
    }

    // TODO add this to the symbols array
    private static $keywords = array("LIMIT");

    /*
     * Check if the token is a keyword
     */

    private function is_keyword($token) {
        return in_array(strtoupper($token), SPECTQLParser::$keywords);
    }

    public function interpret() {
        $querystring = $this->querystring;
        $tokenizer = new SPECTQLTokenizer($querystring, array_keys(self::$symbols));
        $this->parser = new parse_engine(new spectql());

        if (!strlen($querystring)) {
            //give an error, but in javascript, redirect to our index.html
            header("HTTP1.1 491 No parse string");
            echo "<script>window.location = \"index.html\";</script>";
            exit(0);
        }
        try {
            while ($tokenizer->hasNext()) {
                $t = $tokenizer->pop();
                if (is_numeric($t)) {
                    $this->parser->eat('num', $t);
                } else if ($t == "'") {
                    $this->parser->eat('string', $tokenizer->pop());
                    $tokenizer->pop();
                } else if (!$this->is_keyword($t) && preg_match("/[0-9a-zA-Z]+/si", $t)) {
                    $t = rtrim($t);
                    $this->parser->eat('name', $t);
                } else if ($this->is_keyword($t)) {
                    $this->parser->eat(strtoupper($t), null);
                } else {
                    //echo "$t is a symbol: $t is translated into " . self::$symbols[$t] . " ||| ";
                    $this->parser->eat(self::$symbols[$t], null);
                }
            }
            return $this->parser->eat_eof();

        } catch (Exception $e) {

            \App::abort(500, "Something went wrong while parsing the query: $e->getMessage()");
        }
    }

}
