The Abstract Filter Layer : Optimizer
=====================================

The Optimizer is *not implemented* yet. (And I don't have time to do it)
But this document will be about what the optimizer COULD do.


Where to implement optimalisation?
----------------------------------
The UniversalOptimizer class has one method: ``function optimize(UniversalFilterNode $tree)``

In that method you get the query, and need to convert the query to something that is more efficiënt...
So, you have to build a new query...

Where can you optimize?
----------------------

 * nested querys without dependencies  
    -> calculate the nested query only once, for the rest of the time, use the answer  
         !!! Need to see it as a completely independent query  
         So, remove it from the query, execute it first. Then, make a new Constant and put that one back in the query.  
         Why? You need to split the execution because we give complete subtrees to the sources.  
            And if you keep it in the tree it can not be optimized... You can not give the containing subtree away...  
    to implement: need to calculate all dependencies (also with aliases) (so the relations between all identifiers)  
    useful: always if possible (!)  [Only possible with nested querys (!)]

 * merge multiple columnSelectionNodes, multiple FilterByExpressionFilters, multiple DataGroupers that are placed after each other  
    useful: not really, it only makes it easier to see/implement other optimalisations

 * where after join which only depends on one table of the join  
    -> put the where before the join.  
          the expression can also be a part of an and-expression  
    to implement: check where after join, in expression traverse all AND's and check dependencies.  
    useful: not always better, sometimes it can be done more efficient (with indexes or so) to first do the join...  
    note: you could first simplify the query (merging, see above)

 * avoid duplicated calculations  
    -> if you have somewhere two times exact the same subquery, AND they do not depend on columns of their parents,  
       then you are in the same situation as 1 except you only have to calculate it once.  
    to implement: same as 1 + check for equality. + create a new kind of UniversalFilterNode which represents some kind of shared calculation.  
    useful: if is occurs. Otherwise searching for this optimalisation could take more time than just caculating the value twice...

 * ...

Conclusion
----------

So, the first one is really useful if you have nested querys.

The others are not always useful.  
And at least in SQL, you can rewrite the query yourself to be more efficiënt...  
(Instead of letting the computer guess it)

And note that calculating optimalisations also takes time...
