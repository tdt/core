Executers for the different kind of filters in the Abstract Filter Layer
===================================================================


This package contains the executers for a specific node in a Universal Filter Tree.

E.g. IdentifierExecuter executes a Identifier...

You can find more information about how these are used during execution in the file ``universalfilters/interpreter/README.md``


If you want to make a new kind of filter, see down.


Files
-----
 - UniversalFilterExecuters.php -> groups all includes...

 - IUniversalFilterNodeExecuter.interface.php -> interface of all executers 

 - AbstractUniversalFilterNodeExecuter.class.php -> abstract top class of all executers

 - BaseHashingFilterExecuter.class.php 
    -> base functionality for DataGrouperExecuter and DistinctFilterExecuter
    (they both search for rows that are the same (for some fields/all fields))

 - BaseEvaluationEnvironmentFilterExecuter.class.php 
    -> base functionality for FilterByExpressionExecuter and ColumnSelectionFilterExecuter.
    (they both have a source and a environment, which they have to combine to give to their expressions)

 - Unary/Binary/Ternary/Aggregator-FunctionExecuter.class.php
    -> base functionality for all unary/binary/ternary/aggregator executers.
    The executers themself can be found in Unary/Binary/Ternary/Aggregator-Executer*s*.php

 - The rest of the names of the executers speak for themself...

Making a new kind of filter
---------------------------

Let's say we want to implement three new filter: 
  *  DistanceSmallerThan(x1, y1, x2, y2, distance)  
     Returns true if the distance from the coordinate (on earth)  (whatLong, whatLat) to (aroundLong, aroundLat) is smaller than radius.
  *  StringConcat(a, b)  
     Returns the ab
  *  Sorting(a) on field1 ascending, field2 ascending, ...

Ok you say, those are indeed all filter. 
Why three of them? Because they are all implemented differently(!). 
The first one is the easiest to implement.
The second one is a bit harder, and the third one is the most "difficult".

### DistanceSmallerThan

Why is DistanceSmallerThan different from the rest? 
DistanceSmallerThan is what I call a combined filter, it does nothing new. 
I mean: you can write InRadius as 

    sqrt((x1-x2)*(x1-x2) + (y1-y2)*(y1-y2)) <= distance

And that's the way we will implement it.  
Why? Because
1. It's less work
2. It will not break any working code
3. It can be converted back to SQL and executed directly on a database without extra modifications. (if you implemented runFilterOnSource)

So, how do we do it?
We go to the file ``universalfilter/CombinedFilterGenerators.class.php`` and add a new method.

   public static function makeDistanceSmallerThanFilter(UniversalFilterNode $x1, UniversalFilterNode $y1, UniversalFilterNode $x2, UniversalFilterNode $y2, UniversalFilterNode $distance)

And in the method we return a new filter that does exactly what we wrote above:

    return new BinaryFunction(
        BinaryFunction::$FUNCTION_UNARY_SQRT, 
        new BinaryFunction(
            BinaryFunction::$FUNCTION_BINARY_PLUS, 
            new BinaryFunction(
                BinaryFunction::$FUNCTION_BINARY_MULTIPLY,
                new BinaryFunction(
                    BinaryFunction::$FUNCTION_BINARY_MINUS,
                    $x1,
                    $x2),
                new BinaryFunction(
                    BinaryFunction::$FUNCTION_BINARY_MINUS,
                    $x1,
                    $x2)),
            new BinaryFunction(
                BinaryFunction::$FUNCTION_BINARY_MULTIPLY,
                new BinaryFunction(
                    BinaryFunction::$FUNCTION_BINARY_MINUS,
                    $y1,
                    $y2),
                new BinaryFunction(
                    BinaryFunction::$FUNCTION_BINARY_MINUS,
                    $y1,
                    $y2)),    
            ));

Now we want the SQLParser to be able to parse our new function. 
As this is a pentairy function, the parser does not support that yet, but you can easilly extend the parser.

You're done...

So CombinedFunctions are really easy to implement. *If you can implement something as a combined function, I suggest you do so.*

### The second example: StringConcat(a,b)

We can not implement StringConcat as a combined function. It's some kind of basic functionality.

But, it is a function!, so it is easier to implement than e.g. the Sorting.

Let's start.

1. We need a new constant in UniversalFilters.php, so you need to add that first.
2. We need the SQLParser to be able to parse our new function, 
   so in the SQLGrammarFunctions, we add a new entry to the mapping from functionname to constant.
3. We go to the file ``universalfilter/interpreter/executers/implementations/BinaryFunctionExecuters.php`` and add the following code:

        class BinaryFunctionStringConcatExecuter extends BinaryFunctionExecuter {

            public function getName($nameA, $nameB){
                return $nameA."_concat_".$nameB;
            }

            public function doBinaryFunction($valueA, $valueB){
                return $valueA.$valueB;
            }
        }

    That's all the code you have to write...
4. Add a mapping from the constant to the classname in ``universalfilter/interpreter/UniversalInterpreter.class.php``.

You're done...

### The last example: Sorting...

This is the hardest to implement from the three examples.

What do we need to do to implement Sorting?

1. We need a totally new kind of node in the UniversalFilters.php that extends from NormalNode.
   (because it has a source)
2. We need to modify the SQL parser (the lime file) to be able to parse "Order By"-statements.
3. We need to add a method to the file ``universalfilter/interpreter/cloning/FilterTreeCloner.php``
   and a method to the file ``universalfilter/interpreter/debugging/TreePrinter.php`` for cloning and debugging...
4. We need to add a new executer in the folder ``universalfilter/interpreter/executers/implementations``.
5. We need to implement the executer.
   How? See the part: How Executers work.

How executers work
------------------

What do executers do?
1. An executer calculates the header of the table he will return by transforming headers of his children.
2. Converts the content the children return to something new and return that too.

How? They all implement the interface IUniversalFilterNodeExecuter.

    function initExpression($filter, $topenv, $interpreter, $preferColumn);
    function getExpressionHeader();
    function evaluateAsExpression();
    function cleanUp();
    function modifyFiltersWithHeaderInformation();
    function filterSingleSourceUsages($parentNode, $parentIndex);

### ``initExpression``
``initExpression`` is always called first on an executer.

In that method you first set: $this->filter = $filter; (used in the abstract parent class)

In ``initExpression`` the executer is supposed to create executers for all his child-filters (the filters he uses as a source or as an expression) (see ``IInterpreterControl::findExecuterFor``) and call ``initExpression`` on all of them.

``initExpression`` is also supposed to calculate the header of the table the executer will return. 
Because he called ``initExpression`` on his child-filters they calculated their header already.
You can ask the header of the children by calling ``getExpressionHeader``.

#### Arguments
$topenv: If all your children are "sources" you can just pass the $topenv down. If you also have sources that are expressions, you should combine your real sources and your topenv into a new environment you pass down. (extend from ``BaseEvaluationEnvironmentExecuter`` in that case)

$filter: this is the UniversalFilterNode the child is going to execute for. It's the same node you gave to the IInterpreterControl when you asked for an executer.

$interpreter: just pass down. It's the ``IInterpreterControl`` mentioned above.

$preferColumn: Do you expect a totaly new table as a source or a just a column of the current table or an expression on columns of the current table? 

#### After you inited the children - building your own header
After you inited the children, you have to create your own header (based upon the headers of the children). A header is an instance of the class UniversalFilterTableHeader.
If you can, you should try to use as much "clone-modified" methods as possible. 
Especially for UniversalFilterTableHeaderColumnInfo this is the way to create a new column.

Save the header in a new classvariable: $this->header.

#### Rules in ``initExpression``
1. If you can access a table (e.g. the Environment contains a table), you are only allowed to access the header. 
   As the content is not initialized yet. (That's why the even the creation of the childEnvironment (``BaseEvaluationEnvironmentExecuter``) is splitted in two parts)
2. Don't call ``evaluateAsExpression`` yet. You should wait with that till you are in your own ``evaluateAsExpression`` method.

### ``getExpressionHeader``
Returns the header calculated in ``initExpression``, thus:  
return $this->header;

### ``evaluateAsExpression``
Here you generate the content for the header you created in ``initExpression``. You return a ``UniversalFilterTabelContent``-instance.

As described elsewhere, a ``UniversalFilterTableContent`` contains rows (``UniversalFilterTableContentRow``).

So, in this method you loop over the rows of the content you get from your children, create new rows, and add values to these rows...

The values in the rows are set by "columnId", you can ask it to the column in the header (it's generated automatically).

#### Rules in ``evaluateAsExpression``
1. You don't keep big arrays in memory, use a BigMap or a BigList for them. (if you would need that)

### Other methods
So, now you implemented the most important methods in the filter.

You also need some other methods for the filter to work.
#### cleanUp
Calls cleanup on the children, and "frees" any data you created in ``initExpression`` you don't need anymore (most of the time nothing).

#### modifyFiltersWithHeaderInformation
Most of this method is implemented in the abstractbase class.
You just call the parent::-method and the method modifyFiltersWithHeaderInformation on the children.

#### filterSingleSourceUsages
What this method does:  
It returns the arrays of bigest subtrees that only use one source.

What you have to do:  
 - call filterSingleSourceUsages on the children.
 - merge the arrays the children return => $arr
 - call $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);

The method combineSourceUsages does the rest.

### The end
So, that's what executers are supposed to do in all their methods and what the "hidden" rules are they follow.


Ideas for future filter
-----------------------

 - Some more basic filters could be useful, like sqrt(), stringconcat(), sin(), cos(), tan(), atan2(), ......, deg2rad, ...
 - An aggregator: ITEM(column, index) (like FIRST, LAST, ...) could be useful...
   Although you could probably implement that as a combined filter with Index and Offset.
   (FIRST and LAST could be implemented that way too...)
 - The CheckInFunction can be implemented as a combined filter. Which is probably better...