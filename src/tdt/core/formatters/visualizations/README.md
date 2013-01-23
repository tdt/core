# Visualizations

A directory in which a user can add all his own visualizations on data. You can only request a certain visualization based on what visualizations are installed on the datatank you are calling your request to.

## Create your own visualization

The visualizations present are not limiting the developer of only using these! You can easily write your own fvisualization by creating a php class that inherits from AFormatter.class.php and placing the file which holds the class into the visualizations folder.
Once you've done that, all you have to do is implement the printHeader() and the printBody() functions!
