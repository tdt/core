# Formatters

A directory in which a user can add all his own formatters. You can only request this format in a datatank where it is installed.

## Create your own formatter

The formatters present are not limiting the developer of only using these! You can easily write your own formatter by creating a php class that inherits from AFormatter.class.php and placing the file which holds the class into the formatters folder.
Once you've done that, all you have to do is implement the printHeader() and the printBody() functions!