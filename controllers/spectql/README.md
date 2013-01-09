#SPECTQL

SPECTQL is a subset of the HTSQL specification. Some examples below can get you on your way. 
Note that for query endpoints, you must add a /endpoint before your resource you want to address. For example

http://mydatatankhost.com/spectql/myresource{field1,field2}:json

## Define your own query language !

Note that you can also implement your own filter-query language, take a look at the the .lime files in the specql and SQL folders. These .lime files describe the grammar of your query language. On every grammar statement there is an evaluation statement, these are the  '{ }' after the piece of grammar. In these brackets you can tell the parser, in PHP code what object should be made in the parsetree. These objects should be instances of classes that are premade for you and can be found in universalfilter/UniversalFilters.php . These objects define how far you can go with your query language, but cover a fairly wide range of functionality such as joins, groups, selectors, and filters. For more information check out the documentation starting in the universalfilter folder.

## SPECTQL 

* Selecting

Using curly brackets you can start selecting inside a resource:
http://data.iRail.be/spectql/NMBS/Stations{id,name,longitude,latitude}:json

 * Sorting

NOTE: Hasn't been ported to our AST just yet, but it's coming!

You can sort a resource by adding a + or - in the selector:
http://data.iRail.be/spectql/NMBS/Stations{id,name-,longitude,latitude}:json
This will sort descending on name. 

* Limiting

NOTE: Hasn't been ported to our AST just yet, but it's coming!

http://data.iRail.be/spectql/NMBS/Stations{id,name-,longitude,latitude}.limit(10):json
This will only give the 10 first elements

* Filtering

http://data.iRail.be/spectql/NMBS/Stations{id,name-,longitude,latitude}?name~'Ghent':json
Will filter on all names that contain Ghent

* Aliasing

http://data.iRail.be/spectql/NMBS/Stations{identificationnumber:=id,station:=name,long:=longitude,lat:=latitude}?station~'Ghent':json

* Aggregators

Just as HTSQL aggregators work, so does SPECTQL.

http://randomhost.be/spectql/theolympics/medals{country,count(*)}:json
Will return the amount of medals per country. NOTE that if you use an aggregator function, your other arguments in the select { } will be used as parameters
to group your data!

* Functions

Just as SQL you can use unairy and tertiary functions in the select{ }
http://randomhost.be/spectql/theolympics/medals{ucase(country},count(*)}:json
Will provide the amount of medals per country, but now with country in upper case letters.

## SPECTQL Grammar (look at the .lime file to see the actual SPECTQL grammar)

The grammar of the SPECTQL query language should look something like the HTSQL specification:


    Here is the grammar of HTSQL::

        input           ::= segment END

        segment         ::= '/' ( top command* )?
        command         ::= '/' ':' identifier ( '/' top? | call | flow )?

        top             ::= flow ( direction | mapping )*
        direction       ::= '+' | '-'
        mapping         ::= ':' identifier ( flow | call )?

        flow            ::= disjunction ( sieve | quotient | selection )*
        sieve           ::= '?' disjunction
        quotient        ::= '^' disjunction
        selection       ::= selector ( '.' atom )*

        disjunction     ::= conjunction ( '|' conjunction )*
        conjunction     ::= negation ( '&' negation )*
        negation        ::= '!' negation | comparison

        comparison      ::= expression ( ( '~' | '!~' |
                                           '<=' | '<' | '>=' |  '>' |
                                           '==' | '=' | '!==' | '!=' )
                                         expression )?

        expression      ::= term ( ( '+' | '-' ) term )*
        term            ::= factor ( ( '*' | '/' ) factor )*
        factor          ::= ( '+' | '-' ) factor | pointer

        pointer         ::= specifier ( link | assignment )?
        link            ::= '->' flow
        assignment      ::= ':=' top

        specifier       ::= atom ( '.' atom )*
        atom            ::= '@' atom | '*' index? | '^' | selector | group |
                            identifier call? | reference | literal
        index           ::= NUMBER | '(' NUMBER ')'

        group           ::= '(' top ')'
        call            ::= '(' arguments? ')'
        selector        ::= '{' arguments? '}'
        arguments       ::= argument ( ',' argument )* ','?
        argument        ::= segment | top
        reference       ::= '$' identifier

        identifier      ::= NAME
        literal         ::= STRING | NUMBER
