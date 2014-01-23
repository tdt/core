The Abstract Filter Layer : ``IUniversalTableManager``
======================================================

The UniversalTableManager implements an abstraction to see everything as a collection of tables.
So, if you want to use this library in some kind of PHP software, you need to implement this interface.

It's a simple interface with four methods, with two of the four methods that are optional.

``getTableHeader($globalTableIdentifier)``
------------------------------------------

Implementing this method is required. What does it do?
It is supposed to give back the header of the table represented by $globalTableIdentifier.

### The globalTableIdentifier... What are possible strings?

You can interpret $globalTableIdentifier any way you want...

For example, it could contain string in the format ``collection_uri.resourcename``,
but it could also contain uri's or some other name for something that returns a table.

*The filter-implementation does not care what kind of string you use for the representation of a table!*

Conclusion: $globalTableIdentifier can be anyting. It's whatever the users use to identify a table.

### What should this method do?

This method should give back an object of the class ``universalfilter/data/UniversalFilterTableHeader.class.php``.
This class contains information about the names of the columns.

To construct a header you call ``new UniversalFilterTableHeader($columns, false, false);``
Where columns is an array of ``UniversalFilterTableHeaderColumnInfo``-objects.

``getTableContent($globalTableIdentifier, $header)``
----------------------------------------------------

This is also a required method. This method gives back the content of the table represented by ``$globalTableIdentifier``.
You also get the $header you created in ``getTableHeader``.

### What should this method do?

This method should give back an object of the class ``universalfilter/data/UniversalFilterTableContent.class.php``.

What does a class like that contain? It contains rows. (of the class ``universalfilter/data/UniversalFilterTableContentRow.class.php``)


#### A row

You create a new row by calling ``$row = new UniversalFilterTableContentRow()``.
Then you can add new values by calling ``$row->defineValue($columnId, $theValue)``.

``$theValue`` is the value you want to add.
``$columnId`` is a bit more complex...
ColumnId's are automatically generated for each column you added to the header in the previous method.
So, you first have to search for the correct columnId in the header, before you can add a value.


The optional methods
--------------------

### ``runFilterOnSource($query, $sourceId)``
If you don't want to implement this method: just ``return $query;``.

### ``getSourceIdFromIdentifier($globalTableIdentifier)``
If you don't want to implement this method: just ``return "";``.

### What are these methods supposed to do?
They are used to pass the Abstract Filter Tree to execute directly to the source.
You can find more information on this in the README.md in ``universalfilter/sourcefilterbinding``
