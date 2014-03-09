Filter abstraction
==================

What is it
----------

The Abstract Filter Layer is a set of PHP classes for filtering and querying data that has been read from a data source. It contains a set of abstract classes that make it easy to introduce new filters and provide the possibility of executing parts of the query on the data source.

Currently this set of classes is being used by the SPECTQL functionality in The DataTank.

### What do you need to do to implement a new query language?
Write a lime file that defines your query language (an example can be found in the spectql/source directory called spectql.lime). After that compile the lime source file into a php file that will create a query tree from the URI using the [lime php software](http://sourceforge.net/projects/lime-php/).

The UniversalInterpreter class interprets the query tree, which means he'll be executing the tree so that the data will be subject to all of the filters declared in the tree.

Compiling the lime file
-----------------------

Download [lime php from sourceforge](http://sourceforge.net/projects/lime-php/), and recompile the lime\_scan\_tokens file as explained in their HOWTO. Then after compiling the lime file, make sure you add the necessary php use statements, otherwise upon execution PHP will not know how to load the classes defined in the lime parser. The following list of statements has to be passed, if you have adjusted the lime file with other classes, do not forget to add their path as well.

use tdt\core\spectql\implementation\universalfilters\BinaryFunction;
use tdt\core\spectql\implementation\universalfilters\ColumnSelectionFilter;
use tdt\core\spectql\implementation\universalfilters\ColumnSelectionFilterColumn;
use tdt\core\spectql\implementation\universalfilters\Constant;
use tdt\core\spectql\implementation\universalfilters\DataGrouper;
use tdt\core\spectql\implementation\universalfilters\FilterByExpressionFilter;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\LimitFilter;
use tdt\core\spectql\implementation\universalfilters\SortFieldsFilter;
use tdt\core\spectql\implementation\universalfilters\SortFieldsFilterColumn;

Flow of execution
-----------------

In order to explain how SPECTQL works, and can serve as a use case to implement your own query language, we'll be using a datasource foo/bar on which a SPECTQL query is being done.


The first stop is the SpectqlController, this controller passes the URI to the SPECTQLParser which sole purpose is to interpret the string and convert it into a query. This query can be seen as a tree of nodes, each node containing some sort of filter or logic that needs to be done. This query is then passed to an instance of the UniversalInterpreter which is able to execute the query tree. It holds a class of UniversalTableManager which holds functionality to manage "tables".

In the universalfilter-sphere, everything is passed between nodes as a UniversalTable, not just PHP objects. Before executing the query tree, the first table is build by reading data from the datasource and putting it in an initial UniversalTable, sometimes accompanied with a UniversalHeader containing column information about the UniversalTable.

From the SpectqlController the interpreter is asked to interpret the query and use the tablemanager to do that.

All of the components that can be used as a node in the query tree are located in the interpreter/executers directory, feel free to add your own and use them in your lime file.

Once the interpreter has done its job, it returns a table which then needs to be converted into a PHP object for the datatank to return. Note that it's perfectly possible to entirely by-pass the datatank and convert the query tree to a SQL, NoSQL, ... query and execute it onto whatever source, query endpoint you see fit. This result can then be returned as an ExternallyCalculatedFilterNode, located in the sourcefilterbinding directory to let the interpreter know that it doesn't need to execute anything, just copy the result.


The Abstract Filter Tree
------------------------

This section describes what a query can be build from.

### A short description of the filters you can use

#### Filter: Identifier
The most basic and most used filter is the Identifier. It has two meanings.
If you use it as a Source for another filter, it represents a table from The DataTank.
E.g. "gentsefeesten.dag15" in the above example is wrapped in an Identifier.

If you use it anywhere else (e.g. in the WHERE part or SELECT part of an SQL-query), it represents a column/a number of columns in the source dataset. In this last interpretation it can also contain '*', which just returns the complete source dataset.

Please note that I use "." as separator and not "/". So it's "package.resource.column" .

#### Filter: Constant
Another very basic filter is the Constant. It returns a column which contains the given constant.

#### Filter: ColumnSelectionFilter
If you need to select columns or build a new table from existing or calculated columns, you use a ColumnSelectionFilter. It needs a source (the filter that is executed before) and an array of ColumnSelectionFilerColumns which contain a filter that return a column and an optional alias.

#### Filter: SortFieldsFilter
Used to sort the table.

#### Filter: DistinctFilter
Removes double rows...

#### Filter: LimitFilter
Keeps a certain amount of rows from a certain offset.

#### Filter: DataGrouper
Groups data on the given fields. You probably want to use aggregator functions after you did the grouping.

#### UnaryFunctions
Input: one column of the data.
Output: a new column (the unary function applied)

Supported unary functions: "to uppercase", "to lowercase", "string length", "round number", "check if null", "boolean not", "sin", "cos", "tan", "asin", "acos", "atan", "sqrt", "abs", "floor", "ceil", "exp", "log", "datetime_parse", "datetime_datepart".

#### BinaryFunctions
Input: two columns of the data.
Output: a new column

Supported Binary functions: "+", "-", "*", "/", "<", ">", "<=", ">=", "=", "!=", "OR", "AND", "match regex" (does arg1 matches arg2 where arg2 is a regular expression in php), "atan2", "log", "pow", "string concatenation", "datetime_parse", "datetime_extract", "datetime_format".

#### TernaryFunctions
Input: three columns of data.
Output: a new column

Supported Ternary functions: "substring", "regex replace".

#### Aggregators
Input: A table or a column or a grouped column
Output: A row or a cell or a column

Aggregators combine multiple rows in one row. They can be used on a full table (eg. Count(*)) or on columns, or on grouped columns.

Supported Aggregators: "average", "count", "first", "last", "max", "min", "sum"

#### CheckInFunction
Checks for each field in the column if it matches a constant in the list.
(Some sort of enum check)

#### Combined Functions
There are also some combined functions you can easily create by using static methods in ``universalfilter/CombinedFilterGenerators.class.php``


#### Conclusion
So, those are the filters you can use to build the filter syntax tree. Have fun implementing your query language!

Combined Functions
------------------

Like `Between`, `Smaller than all`, ...

see `CombinedFilterGenerators.class.php`

Implementation of the Interpreter
---------------------------------
To understand the implementation of the Interpreter, see the README.md in the folder universalfilter/interpreter to start.

Visualizing the syntax
-----------------------

For those of you who want to visualize our backus-naur notation, can use the following code:

segment             ::= resource '{' (selector | '*') '}' ('?' filter)? (':' format)? END

resource             ::= collection '/' resource_name
selector              ::= argument (',' argument )*
filter                   ::= comparison (('&' | '|') comparison) *
format                ::= 'json' | 'xml' | 'php'

collection            ::= literal ('/' literal)*
resource_name   ::= literal

argument            ::= (function '(' path ')' | path )

function              ::= 'avg' | 'count' | 'first' | 'last' | 'max' | 'min' | 'sum' | 'ucase' | 'upper' | 'lcase' | 'lower' | 'len'
path                   ::= literal ('.' literal )*

comparison        ::= path ('~'  | '<=' | '<' | '>=' |  '>' | '==' | '!=' ) "'" literal "'"

identifier            ::= NAME
literal                 ::= STRING | NUMBER
