# Formatters

A directory in which a user can add all his own formatters. The classes defined all provide in the functionality of transforming PHP objects to structured data formats such as json.


## Create your own formatter

The formatters present are not limiting the developer of only using these! You can easily write your own formatter by creating a php class that inherits from AFormatter.class.php and placing the file which holds the class into the formatters folder.
Once you've done that, all you have to do is implement the printHeader() and the printBody() functions! For a look at our coding conventions, take a look at the README.md of the main folder, or go to [fig standards](http://www.php-fig.org/).