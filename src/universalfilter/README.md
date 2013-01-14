The Abstract Filter Layer
=========================

What is it and why would you use it?
------------------------------------

The Abstract Filter Layer is a module for filtering and querying the data from The DataTank. It is some sort of abstraction to make it easier to implement new kinds of filters with minimal effort.

At the moment of writing this documentation two filters are implemented using this abstraction layer: SQL and spectql.


### What do you need to do to implement a new query language?
Answer: convert the query to the Universal Query Syntax Tree, give it to the UniversalInterpreter, and you are done. Yes, it's that easy.

The UniversalInterpreter interprets the query tree, filters rows, evaluates expressions, matches regexes, filters columns, groups data on certain fields, calculates aggregated values on those fields, ... You don't have to do all those things yourself. So, implementing a new filter becomes a lot easier!


An example: The SQL filter. And how it is executed...
-----------------------------------------------------

*Note:* this is an example of how I converted SQL-querys to the Filter Syntax Tree, but you can do it your way. (It does not matter how you convert it to the syntax tree, or how the user inputs the query.)

For this example we assume we have a resource "gentsefeesten/dag15" in The DataTank.

### Requesting the data...
Assume the user wants the titles and the description of all events from day 15 of the Gentse Feesten where the title starts with 'Bloem'

In SQL this is: 

    SELECT Titel, Omschrijving FROM genstefeesten.dag15 WHERE Titel LIKE 'Bloem%'

To give this SQL-query to The DataTank, we first need to url-encode it. You can use online tools for this. (or the javascript encode()-function) Encoded this becomes:

    SELECT%20Titel%2C%20Omschrijving%20FROM%20genstefeesten.dag15%20WHERE%20Titel%20LIKE%20'Bloem%25'

Add it to the url for sql, and we have:

    localhost/The-DataTank/sql.csv?query=SELECT%20Titel%2C%20Omschrijving%20FROM%20genstefeesten.dag15%20WHERE%20Titel%20LIKE%20'Bloem%25'

Surf to this url and you get back the data you want.

### The flow this query follows inside The-DataTank
When the request enters The-DataTank, it first enters the router. In the router the request gets send to the SQLController.
The SQLController creates an SQLParser and asks it to parse the SQL-query. That's where the conversion from string to Filter Syntax Tree happens. I will go deeper into this in the next paragraph.
The SQLParser returns a Syntax Tree. 

The SQLController makes a new UniversalInterpreter and calls the method interpret($tree) on it. This method returns the dataset in the intern representation of the Abstract Filter Layer (but that's not really important). 
Now the SQLController creates a new TableToPhpObjectConverter and asks it to convert the table to a php object as it is used by The DataTank.

Now, you can use the formatters to format the data. Filtering done...


### The SQLParser
So the only component I did not explain in detail in the above description is the SQLParser. It has only one task: convert the string which represents an SQL-query to the Filter Syntax Tree. To do this I use lime-php. That's a php library to describe and parse context free grammars. It uses a notation that looks like Bachus Naur Form, but than with php-statement which tell the parser what to do if it matches a certain part.
Before I input the data into the parser I already split the query in "tokens" in the SQLTokenizer. In theory, you don't need to do this, as you can pass each character to the grammar and then tokenize there. But e.g. constants (which can contain every character EXCEPT ') are not very handy to describe in a grammar. That's why I first tokenize these basic parts.
If you need examples for the parsing: both SQL and spectql use the lime-parser and have almost the same structure. First they use a SQL/SpectqlTokenizer, then they parse the grammar with a .lime grammar file. These two steps are combined in the class SQLParser/SpectqlParser.


The Filter Syntax Tree (also Universal Filter Tree)
---------------------------------------------------

This section describes what a query can be build of. 
All the possible nodes are defined in one file for ease of use: "universalfilters/UniversalFilters.php".

You should really check out that file for the kinds of filters you can use.

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


Using this library outside of The-DataTank
------------------------------------------
Can I use this query-library outside The-DataTank? Yes, that's possible!

There is only one interface you have to re-implement: IUniversalTableManager.
For more information on how to implement this interface, see the README.md in the folder universalfiler/tablemanager.

The files you can reuse: all the other files in the folder universalfilter + the Parsers for SQL an Spectql.


Passing the Abstract Filter Tree to execute directly to the source
------------------------------------------------------------------

If your source is a database or something else that can execute some kind of query's,
it can be usefull to execute those queries directly on the source 
instead of letting the interpreter download the full resource and then executing the query in php...

The UniversalInterpreter has support for this approach. 
See the README.md in the folder universalfilter/sourcefilterbinding and 
the README in the folder universalfilter/tablemanager about how to implement the tablemanager to support this.


Future development
------------------

### There is also no filter yet: 
1. to sort the data. (order multiple columns ascending/descending)
2. to join data (full/left/right inner/outer join)
3. for Union
4. for Limit+Offset
5. missing functions: functions on dates...

If you want to implement new kinds of filters in the Abstract Filter Layer:
see the documentation of the interpreter in universalfilters/interpreter (for a global overview)
and the documentation about the executers in universalfilters/interpreter/executers.

### Other future developments:
- there are no datatypes. It can be usefull to keep information about the datatype in the tree, so that booleans can be used as numbers, and date's can be compared but also printed without problem. You will also need functions to convert datatypes if you implement this.

### Memory optimalisation for big datasets(*):
The Aggregators are NOT optimized for big datasets(*) (except for count). 
They first convert a column to an array and use build-in phpfunctions. (but arrays are kept completely in memory)

BigDataBlockManager could be implemented more efficiently...

(*) big datasets = Datasets that do not fit in memory

See the README in universalfilters/common for more information about BigDataBlockManager.  
See universalfilters/interpreter/executers/implementations/AggregatorFunctionExecuters.class.php if you want to optimize the Aggregators.

### Query optimalisation:
See README in universalfilters/interpreter/optimizer if you think about implemening that.
