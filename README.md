# Installation

To install The DataTank core, the best practice is to install [tdt/start](https://www.github.com/tdt/start), it contains an installer that load the necessary components to make the datatank structure work. Currently it will install [tdt/framework](https://www.github.com/tdt/framework) and [tdt/core](https://www.github.com/tdt/core).

This can be done by using [composer](http://getcomposer.org/) and performing <b>composer install</b> in the directory of the tdt/start location. You can recognize this location by the presence of a composer.json file.

If you're planning on using the tdt/core as stand alone, you'll have to use the configuration of tdt/start and a mapping of the routes to their respective regular expression. This information can be found on [here](https://github.com/tdt/start/blob/master/app/config/cores.example.json).

# Create instances

See examples/

# tdt/core's purpose

The DataTank's purpose is to open up data via a set of parameters (i.e. where is your datafile located), and return it to a user in a certain format. Next to that users can also perform
queries on top of that data, resulting in a more specific data set returning in the response.

# Structure

### controllers

The controllers folder is where the magic begins. If you use tdt/start, a HTTP request finds it way to the datatank and via the configuration of tdt/start/app/config/cores.json the given URL will be passed
to a controller, according to a certain regular expression in cores.json of tdt/start.

example:
```
"GET | (?P<packageresourcestring>.*)\\.(?P<format>[^?]+).*" : "controllers\\RController"
```

This will lead a URL passed by a HTTP GET request, existing out of any given string ending with a dot followed by a string representing a format, to our RController. This controller will then apply further logic to provide
this request of an answer.

If you want to use The DataTank core without the use of tdt/start, you can still fill out the config of tdt\framework\Config::setConfig($array) documented in the README of the tdt/framework.

### formatters

This folder contains classes that transform PHP data objects into formatted data structures such as json. In the next few weeks this functionality will be put into tdt/formatters. This repository
will then be included in this projects by forseeing an entry of tdt/formatters in the composer.json file. This will allow for 3rd parties to use tdt/formatters as package to transform PHP objects
and hopefully contribute to this package.

### lib

The lib folder holds libraries that are not avaible through composer, packagist or any other autoload program. Currently it contains a dependency that holds the parse engine that allows us to build our own query language
called spectql.

### model

The model folder is one of the largest in the entire structure. It allows in functionality to adjust, create and delete and get definitions of resources. It basically puts a layer on top of
the functionality of CRUD operations and provides an interface on it, ready for other classes to use. What will happen most of the time is that a controller performs its logic given a certain URL, then call upon
the functionality of the model/ResourcesModel for further handling.

> #### filters

> This folders contains some logic to apply small filters to data.

> #### packages

> This folders contains resources that are considered _core_ _resources_. These resources provide information about the tdt/core and are default accessible resources when the core is installed.

> #### resources

> This folder contains classes that perform further CRUD logic for a given definition of a resource.

> #### semantics

> This class contains code that allows for semantic operations. Currently this hasn't been tested just yet. It's purpose is to allow for semantic output, and probably will be separated into the tdt/formatters repository.

### strategies

This folder contains classes that represent different data source files extractors. They also validate the data source, if any errors show up during this proces a resource definition might be
denied from creation.

### universalfilter

This folder contains a large set of classes that allow for querying on top of PHP objects.

# Coding standards

In order to code properly we use some standards such as:

* one indent = four spaces

* every php files starts with a comment section explaining what the file does, what the author is
  to whom the copyright belongs and what license it holds.

* functions are camelCaseNotation()

* variables are also $camelCaseNotation ( although you might see an _ notation here and there, this is mostly when we query variables, if not...it's our fault and bad practise and you should learn from our mistake :) )

In future releases we will be using the [fig standards](http://www.php-fig.org/).

# Read more

If you want to read more about The DataTank please visit http://thedatatank.com for developer documentation, and http://thedatatank.com for introductions.

The DataTank is free software (AGPL, © 2011,2012 iRail NPO, 2012 OKFN Belgium) to create an API for non-local/dynamic data in no time.

Any questions? Add a support issue.

-Pieter, Jan and Lieven
