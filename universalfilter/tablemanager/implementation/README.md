Where The Abstract Filter Layer and The DataTank come together...
=================================================================

This folder contains the implementation of the interface ``universalfilter/tablemanager/IUniversalFilterTableManager.interface.php`` specific for The-DataTank.

If you want to use The Abstract Filter Layer in another software-package, you need to reimplement the interface ``IUniversalFilterTableManager``.
You can find more information on how to do that in ``universalfilter/tablemanager/README.md``.



Identifiers for tables
----------------------
(as interpreted by the UniversalTableManager)

If you have a resource `data` in a package `testpackage`, the name of the table is: `testpackage.data`.

If you also have restparameters it becomes: `testpackage.data.restparam.restparam`.

With multiple packages: `testpackage.subpackage.data.restparam.restparam`.

Hiërarchical data is divided in multiple tables. E.g. if this data is placed in `/package/test/`:
    <root>
        <a x="x" y="y">
            <z d="d"/>
        </a>
        <a x="x" y="y">
            <z d="d"/>
        </a>
        <a x="x" y="y">
            <z d="d"/>
        </a>
        <b x="x" y="y">
            <z e="e"/>
        </b>
    </root>

You have a table `package.test`:
    | index |   value    |
    |   a   | <<object>> |
    |   b   | <<object>> |

You have a subtable for "a": `package.test:a`:
    |   x   |   y   |     z      |
    |   x   |   y   | <<object>> |
    |   x   |   y   | <<object>> |
    |   x   |   y   | <<object>> |

And also a subtable "a.0.z": `package.test:a.0.z`:
    |   index   |   value   |
    |     d     |     d     |


About the implementation
------------------------

### So, we need to convert the data in The DataTank to tables...

The implementation for tabular data is straightforward. 

For the conversion from php-object to the table and back: see universalfilters/tablemanager/implementation/tools for the conversion classes

How do we transform PHP-objects to tables ?
-------------------------------------------

This transformation will be explained by a series of examples. First lets assume that we do not
go deeper into an object. (e.g. we do not zoom in on a piece of the object we want to query.) The 
return value I get from a the ResourceModel will either be an stdClass (object) or an array.

Case 1) We get an object.  
    In case of an object, the names of the datamembers are important, and have to be used, not thrown away. Ofcourse
    the values of these datamembers are important, and have to be saved as well. This will result in a somewhat hash-map
    like datastructure ( e.g. { field1:"value1", field2:"value2" } ).  
    Resulting table:  
    <table>
        <tr>
            <th>index</th><th>value</th>
        </tr>
        <tr>
            <th>field1</th><th>value1</th>
        </tr>
        <tr>
            <th>field2</th><th>value2</th>
        </tr>
    </table>    
Case 2) We got an array.  
    With arrays we make a distinction between associative arrays and numerical arrays.   
    We define an associative array when minimum 1 field is a not a number. In **`numerical arrays`** the values kept in it are just like rows in the table. Thus, the values have to be interpreted as rows.    
    a) If an object is the value of a numerical index then all the fields of this object become columnheadernames.  
        e.g.:  
          [  
            {field1:"value1", field2:"value2"},  
            {field1:"value1b", field2:"value2b"}  
          ]  
       Resulting table:  
       <table>
        <tr>
            <th>field1</th><th>field2</th>
        </tr>
        <tr>
            <th>value1</th><th>value2</th>
        </tr>
        <tr>
            <th>value1b</th><th>value2b</th>
        </tr>
       </table>  
    b) If the numerical index's value contains a string of some sort then the table has a column named "value".  
        e.g.:     
            [  
              "string1",  
              "string2"  
            ]  
       Resulting table:  
       <table>
        <tr>
            <th>value</th>
        </tr>
        <tr>
            <th>string1</th>
        </tr>
        <tr>
            <th>string2</th>
        </tr>
       </table>              
    c) If the value is an array, the columnames become "index_"+$i.  
        e.g.:   
            [  
              ["string1", "A" => "B"],  
              ["string2"]  
            ]  
       Resulting table:        
       <table>
        <tr>
            <th>index_1</th><th>index_A</th>
        </tr>
        <tr>
            <th>string1</th><th>B</th>
        </tr>
        <tr>
            <th>string2</th><th>null</th>
        </tr>
       </table>  
    With associative arrays the indexes, or more correctly keys, are probably also important.  
    Note that we don't save the information of "keys" in transforming numerical arrays to tables.  
    To save the "key" information on which a certain value is mapped, we add an extra "index" column to the resulting table.  
    The rest of the info is transformed to a table in the exact same way as numerical arrays are being transformed.  
       e.g.:  
           [  
             "SomePK1" => {veld1:"value1", veld2:"value2"},  
             "SomePK2" => {veld1:"value1b", veld2:"value2b"}  
           ]  
       Resulting table:  
       <table>
        <tr>
            <th>index</th><th>field1</th><th>field2</th>
        </tr>
        <tr>
            <th>SomePK1</th><th>value1</th><th>value2</th>
        </tr>
        <tr>
            <th>SomePK2</th><th>value1b</th><th>value2b</th>
        </tr>
       </table>   

### We also implemented the runFilterOnSource method to run filters directly on the source.

For more info, ask Jan ;)

Ideas about future development
------------------------------

 - Add ".?" tables.
   E.g. If you have a resource ``gentsefeesten.dag15``, with columnNames: Titel, Datum, ...
   Then the table: ``gentsefeesten.dag15.?`` would return the following table:
   
   <table>
      <tr>
         <th>Field</th>
      </tr>
      <tr>
         <td>Titel</td>
      </tr>
      <tr>
         <td>Datum</td>
      </tr>
      <tr>
         <td>...</td>
      </tr>
   </table>
   