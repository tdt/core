The Abstract Filter Layer : Common Functionality
================================================

This package contains some common functionality that is used through the layer.

BigList, BigMap and BigDataBlockManager
---------------------------------------

I use this classes in the implementation of the filter so I don't have to worry about very big datasets. 
If that's the case, you just have to implement these classes efficiently...

### BigList
Implements a list that can possibly grow very big.

Current implementation:
Divides the list in blocks of 5 items and saves those individualy in the BigBlockDataBlockManager...

### BigMap
Implements a map that can possibly grow very big.

Current implementation:
Saves each entry as a block in the BigDataBlockManager

### BigDataBlockManager

This class is used by BigList and BigMap. 
Note that the name for this class is not really correct. 
A better name for this class would be "LotsOfDataBlocksManager". 
As most of the blocks are very small, but there can be a lot of them.
(The meaning of this class has changed a bit over time...)

You can give this class a block of data with a name and then later ask a block again (by it's name).
The BigDataBlockManager keeps a very big number of blocks of data in memory, but if there are more it writes them to file.

#### Sidenote: why did the meaning change?
The old BigDataBlockManager kept 30 blocks of big data in memory. 
If it had to keep more blocks it wrote one to file.
BigList was implemented to give each 20000...? lines as a block to the BigDataBlockManager.

And that worked, even for very big BigLists.

But it had a problem, what if we make a lot of small BigLists. Al those small BigLists would take a whole block of space (thus space for 2000...? lines) while they had only like 3 e.g. lines. 
Thus the BigDataBlockManager would start writing things to file even though there were only like 100 lines in memory...

So, grouping in the BigList was not going to work.

That's why I moved the grouping in big blocks of data to the BigDataBlockManager.

### The current implementation

BigDataBlockManager is not implemented in the best possible way...

Problem: If it has to write things to file it creates one file for each block. (so it does nog group them yet)
For a map that means a file for each entry, and for a list: a file for each 5 rows.
BUT: it does keep a lot of things in memory, so he almost never starts writing things to file...

If you want the files to be bigger, 
the best way to implement the BigDataBlockManager is to implement it as a Lineair Hashing Table (<http://en.wikipedia.org/wiki/Linear_hashing>)
where each "bucket" is a file...

Other things...
---------------

HashString contains a global function for converting a string to an unique string that does not contain special characters.
It is used in the BigDataBlockManager (for mapping the name of a block to a file) and in the BaseHashingFilterExecuter (for grouping and distinct).