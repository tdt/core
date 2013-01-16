/*
 * About the grammar.lime file
 *
 * @package The-Datatank/controllers/SQL
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

 - lime is used to compile SQLgrammar.lime to SQLgrammar.php

 - About the grammar: 
    Not in the grammar: 
       identifier = parsed by tokenizer
       constant = parsed by tokenizer
       keywords (one category for each) = parsed by tokenizer
    
 - Note about the grammar:
    - union always requires () around both select statements.

