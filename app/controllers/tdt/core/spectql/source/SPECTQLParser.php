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

class SPECTQLParser
{

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
        "/" => "'/'",
        "*" => "'*'",
        ":" => "':'",
        "," => "','",
        "^" => "'^'",
        "%" => "'%'",
        "?" => "'?'",
        "'" => "SQ"
    );

    public function __construct($querystring) {

        // url decode
        // http://php.net/manual/en/function.urldecode.php
        // we use a + sign to use order functionality, but decode will translate
        // a + to a whitespace, so lets first translate the + sign to it's urlencoding (%2B)
        $querystring = str_replace('+', '%2B', $querystring);
        $querystring = str_replace('%26', '&', $querystring);

        $this->querystring = ltrim(urldecode($querystring), "/");
    }

    private static $keywords = array("LIMIT");

    /*
     * Check if the token is a keyword
     */
    private function is_keyword($token) {
        return in_array(strtoupper($token), SPECTQLParser::$keywords);
    }

    /**
     * Interpret the query by parsing the tokens
     */
    public function interpret() {

        $querystring = $this->querystring;
        $tokenizer = new SPECTQLTokenizer($querystring, array_keys(self::$symbols));
        $this->parser = new parse_engine(new spectql());

        try {
            while ($tokenizer->hasNext()) {
                $t = $tokenizer->pop();
                if (is_numeric($t)) {
                    $this->parser->eat('num', $t);
                } else if ($t == "'") {
                    $this->parser->eat('string', $tokenizer->pop());
                    $tokenizer->pop();
                } else if (!$this->is_keyword($t) && preg_match("/[0-9a-zA-Z\s-_]+/si", $t)) {
                    $t = rtrim($t);
                    $this->parser->eat('name', $t);
                } else if ($this->is_keyword($t)) {
                    $this->parser->eat(strtoupper($t), null);
                } else {
                    $this->parser->eat(self::$symbols[$t], null);
                }
            }
            return $this->parser->eat_eof();

        } catch (\Exception $e) {
            $message = $e->getMessage();
            \App::abort(500, "Something went wrong while parsing the query: $message");
        } catch(parse_error $e){
            $message = $e->getMessage();
            \App::abort(500, "Something went wrong while parsing the query: $message");
        }catch(parse_bug $e){
            $message = $e->getMessage();
            \App::abort(500, "Something went wrong while parsing the query: $message");
        }
    }

}
