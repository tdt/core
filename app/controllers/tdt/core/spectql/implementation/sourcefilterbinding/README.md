The Abstract Filter Layer : Executing a query directly on the source
====================================================================

The Abstract Filter Layer allows you to execute querys directly on a source that supports some kind of filtering.

There are two methods in the IUniversalTableManager that are used to communicate the query to the "outside" (outside the interpreter)

See also the documentation in ``universalfilter/tablemanager/README.md`` for information about how to implement the UniversalTableManager.

What does it do?
----------------
Executing a query directly on the source can be useful in the case of a database where you don't want to download the full table.

The Abstract Filter Layer allows you to do that by looking at the query and giving the piece of the query that can be executed on one source to the UniversalTableManager.
If the UniversalTableManager does not want/can not to execute this query on the source, he can just return the query.
If he decides he can execute parts of the query directly on the source, 
he can convert those parts of the query to for example SQL, execute them on the database, 
convert the answers back to the intern representation and replace the parts of the query he executed by ``ExternallyCalculatedFilterNode``s.

If the interpreter then later finds a ExternallyCalculatedFilterNode in the query he will use the anwser saved in that node instead of asking the full table and calculating that part of the query himself.
So, if you pre-calculated all parts of the query (or at least all parts that contain a identifier that represent a table), the interpreter will not call ``getTableContent``...

``runFilterOnSource($query, $sourceId)``
----------------------------------------
So this method gets the part of the query that can be executed completelly on the source with id $sourceId.

So, it get called for every source (at least once). 
But how does the interpreter knows which tables are in the same source? 
(Because, as said in ``universalfilter/tablemanager/README.md`` the interpreter does not care about how tablenames look...)

Well, he calls the method ``getSourceIdFromIdentifier``. If it returns the same string, the tables are in the same source.

The return id is also given to the method ``runFilterOnSource`` (``$sourceId``)

#### ``ExpectedHeaderNamesAttachment``
If you decide to run the filter on the source, and you get back some data. 
You need to convert it to a table... (so far, nothing new)

But, as the universalfilter needs to know which columns contains which data, you need to name the columns as described in the ``ExpectedHeaderNamesAttachment``.

##### How do you get a ``ExpectedHeaderNamesAttachment``?
Every filter has one attached to it. So you need to get the one from the filter you execute...

You can ask it by doing: ``$filter->getAttachment(ExpectedHeaderNamesAttachment::$ATTACHMENTID);``.

``getSourceIdFromIdentifier($globalTableIdentifier)``
-----------------------------------------------------
This method gives back some kind of identifier for the source the table in ``$globalTableIdentifier`` is in.


Looks easy, I just have to implement runFilterOnSource...
---------------------------------------------------------
Yes, but it is not easy. Converting an Universal Syntax Tree to for example SQL can be quite challenging :)
