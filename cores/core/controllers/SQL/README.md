Parser for SQL querys
=====================

This folder contains the parser for the SQL-querys you can give to The DataTank.

INPUT: string that represents a SQL-query  
OUTPUT: a Filter Syntax Tree. (as described in universalfilters/README.md)


What can this parser parse and convert to the Universal Syntax Tree?
--------------------------------------------------------------------

It can parse and convert:
 - `SELECT ...` with optional aliases for columns
 - `FROM` (but universalfilters supports no joins yet, so no ",")
 - `WHERE e`
 - `GROUP BY field, field, field`
 - `LIKE`
 - `IN (...)`
 - `LIMIT ... [OFFSET ...]`
 - `ORDER BY field ASC/DESC, field ASC/DESC, ...`

 - nested Selects

 - Math: `"+"`, `"-"`, `"*"`, `"/"`
 - Comparision: `"<"`, `">"`, `"<="`, `">="`, `"<>"`, `"!="`, `"="`
 - Boolean operations: `"AND"`, `"OR"`, `"NOT"`
 - String concatenation: `'|'`

 - Functions:
   * Unary: `"UCASE(_)"`, `"LCASE(_)"`, `"LEN(_)"`, `"ROUND(_)"`, `"ISNULL(_)"`, `"SIN(_)"`, `"COS(_)"`, `"TAN(_)"`, `"ASIN(_)"`, `"ACOS(_)"`, `"ATAN(_)"`, `"SQRT(_)"`, `"ABS(_)"`, `"FLOOR(_)"`, `"CEIL(_)"`, `"EXP(_)"`, `"LOG(_)"`  
     _note: `"ISNULL(_)"` returns `true` or `false`_

   * Binary: `"MATCH_REGEX(_,_)"`, `"ATAN2(_,_)"`, `"LOG(_,_)"`, `"POW(_,_)"`  
     _note: `MATCH_REGEX(_,_)`: see also http://www.php.net/manual/en/function.preg-match.php_

   * Ternary: `"MID(_,_,_)"`, `"REPLACE_REGEX(_,_,_)"`  
     _note: `REPLACE_REGEX(_,_,_)`: see also http://php.net/manual/en/function.preg-replace.php_

   * Other: `"GEODISTANCE(latA,longA,latB,longB)"`

   * Aggregators: `"AVG(_)"`, `"COUNT(_)"`, `"FIRST(_)"`, `"LAST(_)"`, `"MAX(_)"`, `"MIN(_)"`

 - Functions on DateTime
   * The syntax of the DateTime-functions mostly follows the mySQL syntax (except for the patterns)  
     See http://dev.mysql.com/doc/refman/5.5/en/date-and-time-functions.html

   * Getting the Date:  
     `"NOW()"` = `"CURRENT_TIMESTAMP()"` = `"LOCALTIME()"` = `"LOCALTIMESTAMP()"`  
     `"CURDATE()"` = `"CUR_DATE()"` = `"CURRENT_DATE()"`  
     `"CURTIME()"` = `"CUR_TIME()"` = `"CURRENT_TIME()"`

   * Parsing of Dates:  
     `"STR_TO_DATE(_string_,_format_)" = "PARSE_DATETIME(_string_,_format_)", "STR_TO_DATE(_string_)" = "PARSE_DATETIME(_string_)"`  
     See http://www.php.net/manual/en/datetime.createfromformat.php for possible formats...  
     See http://www.php.net/manual/en/datetime.formats.php for the interpretation of the date when no format is given...

   * Formating of Dates:  
     `"DATE_FORMAT(date,format)"`  
     See http://www.php.net/manual/en/function.date.php for possible formats...

   * Extracting...  
     `"EXTRACT(unit FROM date)"`  
     Possible unit-values: `SECOND`, `MINUTE`, `HOUR`, `DAY`, `WEEK`, `MONTH`, `YEAR`, `MINUTE_SECOND`, `HOUR_SECOND`, `HOUR_MINUTE`, `DAY_SECOND`, `DAY_MINUTE`, `DAY_HOUR`, `YEAR_MONTH`

   * Modifying dates...  
     `"DATEPART(_)"` = `"DATE(_)"`, 

   * More functions...  
     `"DAY(_)"`, `"DAYOFMONTH(_)"`, `"DAYOFWEEK(_)"`, `"DAYOFYEAR(_)"`, `"HOUR(_)"`, `"MINUTE(_)"`, `"MONTH(_)"`, `"MONTHNAME(_)"`, `"SECOND(_)"`, `"WEEK(_)"`, `"WEEKOFYEAR(_)"`, `"WEEKDAY(_)"`, `"YEAR(_)"`, `"YEARWEEK(_)"`

Tablenames...
---------
If you want to know *how tables are named*: (you should know this!!!)

See /universalfilter/tablemanager/implementation/README.md


How I parse SQL querys
----------------------

example: ``SELECT * FROM package.resource``


 1. SQLTokenizer => split query in tokens
 
        "SELECT", "*", "FROM", "package.resource"
    
 2. SQLParser => categorize the tokens and give the tokens to the grammar
 
        "SELECT"        => category SELECT,     value null
        "*"             => category '*',        value null
        "FROM"          => category FROM,       value null
        "package.table" => category identifier, value "package.resource"
    
 3. SQLgrammar => build the tree.

        ...result:
        ColumnSelectionFilter(
            new ColumnSelectionFilterColumn(
                new Identifier("*"), null));
        ...after...
        Identifier("package.resource");


SQLgrammar.lime
---------------

The SQLgrammar is writen in lime-php. That's a php library to describe and parse context free grammars. It uses a notation that looks like Bachus Naur Form, but than with php-statements which tell the parser what to do if it matches a certain part.


Limits of the current SQL Parser
--------------------------------

- Joins, Sorting, Union and Limit+Offset are not supported, as the Abstract Filter Layer does not support that yet.
- IS NULL or IS NOT NULL are not implemented and NULL is not a constant...
- There are no datatypes, and no functions for dates yet.
- bug? tokenizer has problems with newlines at the end of the query (???)