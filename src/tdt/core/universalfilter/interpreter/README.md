The Implementation of the interpreter for the Abstract Filter Layer
===================================================================

NOTE
----
If you just want to implement a new query-language, you don't _need_ to read this file. In that case, go to the file: "universalfilter/README.md".


The idea
--------

So, someone gives us a Filter Syntax Tree, and we need to evaluate it. How are we going to do that? 
First problem: a "Filter Syntax Tree" is a bit abstract. So, let's try to focus on SQL. How would we implement SQL? 
SQL works on tables. Well, so does this interpreter. (The tables can be extended to be something else than a table though) 

So, the first thing we need to do is convert all the data inside The DataTank to tables. For csv's, excel, and other kinds of tabular data that's not a problem. But what about xml?
We don't want to worry about that while implementing the filters, so we have an abstraction named the ``UniversalFilterTableManager`` which does the conversion and the rest of the code does not know how it happened.

### The ``IUniversalFilterTableManager`` interface

Information about implementing this interface for other software than The DataTank can be found in the README.md in ``universalfilter/tablemanager``.

Information about the current implementation of this interface for The DataTank can be found in ``universalfilter/tablemanager/implementation/README.md``.

### The ``UniversalFilterTable``

We also need some kind of structure/class to save the data in. I called it the ``UniversalFilterTable`` and it is located in the "universalfilter/data" folder.

The ``UniversalFilterTable`` is build of two parts: ``UniversalFilterTableHeader`` and ``UniversalFilterTableContent``. 

The ``UniversalFilterTableHeader`` contains information about the table but not the content. So it contains the names of the columns, information about links to other tables, whether the table contains exactly one column or if it could contain more than one,... (same for rows)
It contains a array of ``UniversalFilterTableHeaderColumnInfo``-objects, which keeps the information for the individual columns. 

Note that the name of a column is not a string but an array. For example the column ``Titel`` in the table of the ``gentsefeesten.dag15`` is actually named ``gentsefeesten.dag15.Titel``.

A column also knows whether it is grouped or not.

It also contains an id. Why? Because there can be two column named Title. And how are you going to identify them otherwise?

The ``UniversalFilterTableContent`` contains the data of the table. 
It is build out of a list of ``UniversalFilterTableContentRow``'s. 

A ``UniversalFilterTableContentRow`` contains the data of one row of the table. (Or multiple if it is grouped). It is saved by the id(!) of the column, not by the name.

I tried to hide the internal structure of the table as much as possible. That's why these classes have a lot of clone-methods or "copyToOtherStructure"-methods. 

You can also find more information about these classes in the README.md in ``universalfilter/tablemanager``

### What now?
So we have a representation for our table, and we can convert the data of The DataTank to our representation.

Now we need to execute the query on these tables.


### Let's start in the ``UniversalInterpreter``

When the user want to evaluate something he calls the interpret($tree) method on this class. 

First we could optimize the tree, but that's not implemented yet. See ``universalfilter/interpreter/optimizer/README.md`` if you want to implement that.

So we start executing it...

### Executing the query. (simplified version, no execution on sources themself)

Evaluation happens in two steps, first we check the tree and create all headers for the tables we will return. Second we execute all querys.

The UniversalInterpreter looks which filter is at the top of the tree and creates an executer for it. 
The interpreter also creates an environment(*) with an empty table and then asks the executer to create his header and execute his filter. 

(*) = see later.

#### In the executer, evaluating the headers (simplified version)

This executer first looks which filter is underneath him, creates an executer for it and asks his header. This executer does the same, and so on. When we reach an identifier the recursion stops and it asks the header to the ``UniversalFilterTableManager`` and returns it. And then we go back up. The filters combine and create new headers and return these. Till we are back at the top.

#### In the executer, evaluating the content (simplified version)

Evaluating content also happens recursive. Filters combine tables and return these...
There are also special kinds of tables (this information is kept in the header): a column is a table, a table with one row is a table, and a table which will always return one column and one row is also a table. (But some need special treatment)

#### What with expressions? => The ``Environment``

ColumnSelectionFilters and FilterByExpressionFilters also evaluate expressions. But these depend on the data in the source. 

So, we first execute the source-filter and then give the result in the ``Environment`` to the expressions. This way they can access columns and combine them in all kind of ways to return true or false (case of the FilterByExpressionFilter) or return a new column (ColumnSelectionFilter).


#### ColumnSelectionFilter also has an enviroment, but it has a source too...

Indeed, but the Environment of the ColumnSelectionFilter is only used if it is used in nested query's. So it is used if there is a ColumnSelectionFilter in the expression of a ColumnSelectionFilter or a FilterByExpressionFilter.

### Direct execution on the source

There is one thing we didn't take in account in the simplified version: direct execution on the source.
The first part is the same, we first calculate all headers in a recursive way.

But after that we first check what we can execute directly on the source.
How do we do that?
1. We ask all executers (recursive) to add there header information to the $query as an attachment.
2. We find out what we can execute on a single source (recursive) (by calling filterSingleSourceUsages on the executers)
3. We give all those things we can execute on a single source to the UniversalFilterTableManager. (method runFilterOnSource)  
   The UniversalFilterTableManager can then either just return the query, 
   or calculate parts of the query and replace those parts by a ExternallyCalculatedFilterNode.
4. We execute the query as usual. Except that the query can also contain a new kind of node: an ExternallyCalculatedFilterNode.

### That's the flow of the execution. 

To see how the individual filters work you could look in the code of the Executers or look at the README.md in the folder ``universalfilter/interpreter/executers``.


