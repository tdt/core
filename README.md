# tdt/core

[![Build Status](https://travis-ci.org/tdt/core.png?branch=development)](https://travis-ci.org/tdt/core)

# Installation

To install the datatank core, the best practice is to install tdt/start. This repository is located at http://www.github.com/tdt/start and contains an installer that load the necessary
components to make the datatank structure work. Currently it will install the requirements for tdt/core found in the composer.json file.

This can be done by using [composer](http://getcomposer.org/) and performing <b>composer install</b> in the directory of the tdt/start location. You can recognize this location by the presence of a composer.json file.


If you're planning on using the tdt/core as stand alone, you'll have to use the configuration of tdt/start and a mapping of the routes to their respective regular expression. This information can be found on [here](https://github.com/tdt/start/blob/master/app/config/cores.example.json).

# Create instances aka resources

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

If you want to use The DataTank core without the use of tdt/start, you can still fill out the config of tdt\core\utility\Config::setConfig($array).

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


## Stand-alone usage

The tdt/core can be installed via composer and provides the following functionality if used without any other tdt/ components:

* define resource definitions to read data such as CSV, XML, SHP files and databases.
* process this data and creating PHP objects out of it

To do this you'll need to used the ResourcesModel class, found in model/ResourcesModel.php. This class represents the resource definition of every resource. It needs a configuration parameter in order to work though, so let's take a look at how this configuration parameter is supposed to look like.

### Making an instance

The ResourcesModel class is a Singleton class, so you'll have to create an instance once with a configuration parameter. This configuration tells our ResourcesModel where to put and get our definitions.
The configuration parameter is an array and, if used stand-alone wise, consists of 2 subarrays namely *general* and *db*.

The *general* entry in the configuration array is again an array consisting of the following key-value pairs:

* timezone => string i.e. Europe/Brussels
* defaultlanguage => string i.e 'en'
* defaultformat => string 'json'
* accesslogapache => string i.e. 'C:\wamp\logs\access.log'
* apachelogformat => string i.e 'common'

* cache => array (system => string 'MemCache' ,'host' => string 'localhost' (length=9),'port' => int 11211)
* faultinjection' => array('enabled' => boolean true,'period' => int 1000)
* auth => array('enabled' => boolean true,'api\_user' => string 'username', 'api\_passwd' => string 'password')
* logging' => array('enabled' => boolean true, 'path' => string 'C:\wamp\www\startLogs')

The *db* entry is also an array containing the following key-value pairs:

* system => string i.e. 'mysql'
* host => string i.e. 'localhost'
* name => string i.e. 'my_database'
* user => string i.e. 'root'
* password' => string i.e. 'password'

Write rights are necessary for the user you pass along in the *db*-entry of the configuration array.

So far so good, you got your resources model. Now in order to read data from resource definitions, you need to use the *createResource* and *readResource* functions.

### createResource

The createResource function takes 2 arguments:

1. parameter-resource-string
2. parameters array

The first is the name under which you want to put your resource definition. Say for example you want to put a definition of a dataset concerning the population of the north pole into your datatank, you might opt to give that string the value "nortpole/population".

The second parameter is an array containing necessary parameters for your resource, aka your definition or your resource. To know what the necessary parameters are for your file you want to publish take a look at TDTInfo/Admin. This resource is a standard given resource containing all the information an admin user can do on a DataTank. A CSV file resource for example will take parameters such as "delimiter" whereas a database resource will take parameters such as db\_password.

### readResource

The readResource function takes 4 arguments:

1. packagename
2. resourcename
3. parameters
4. RESTparameters

The first two parameters are pretty straightforward, let's say that we want to read our previously added northpole population data source we have to give packagename and resourcename the values "northpole" and "population" respectively.

The third parameter is an array containing read parameters, these are key-value pairs that you would normally pass along in the query string in a URL. If these are applicable fill those in, otherwise pass along an empty array.

The fourth parameter is RESTparameters, and is used to dig into an object. For example:


northpole/population returns this data object:

object=> populationdata => array(region1 => some data
                   ,region2 => some data
                   ,region3 => some data )

Now if I only want data about region1 I can pass along with the RESTparameter parameter an array which holds a 2 strings: "populationdata", "region1". This will return only the data about region1.

# Coding standards

In order to code properly we use some standards such as:

* one indent = four spaces

* every php files starts with a comment section explaining what the file does, what the author is
  to whom the copyright belongs and what license it holds.

* functions are camelCaseNotation()

* variables are also $camelCaseNotation ( although you might see an _ notation here and there, this is mostly when we query variables, if not...it's our fault and bad practise and you should learn from our mistake :) )

In future releases we will be using the [fig standards](http://www.php-fig.org/).

# Common questions and requirements

## Requirements

* PHP 5.3 or higher
* Apache
* mod_rewrite
* MySQL database back-end with write permission

## Common questions

This section will list some obstacles encoutered by the developers and users, and how to resolve the obstacle.

### Wampserver

This section covers some problems encountered with The DataTank installed on a Wampserver stack.

#### cURL execution of the TDTAdmin/Export not working

We could just let the user search for this problem by themselves, as it's a bit far from the datatank's focus. However, since export is a very handy functionality we want to make sure the user can
use it without much trouble. The one problem we've encountered with this is that the cURL binaries on wampserver aren't always the correct ones. The thing you should do is replace them by pre-compiled ones.

First of all shut down your wampserver completely, as replacing these binaries might cause inconsistencies in the log files of the wampserver that will cause failures while restarting the stack.
After that you can visit a [this website](http://www.anindya.com/php-5-4-3-and-php-5-3-13-x64-64-bit-for-windows/) containing proper pre-compiled binaries and look under the section _Fixed_ _curl_ _extensions_.
There you should download php\_curl-5.3.13-VC9-x64.zip if you're running a wampserver with PHP 5.3.13 or php\_curl-5.4.3-VC9-x64.zip if you're running wampserver with PHP 5.4.3.

Replace these binaries in the folder _wamp/bin/php/php\_version/ext_ and start up your wampserver. This should enable you to execute the generated PHP PUT scripts created by TDTAdmin/Export.

# Read more

If you want to read more about The DataTank please visit http://thedatatank.com for developer documentation, and http://thedatatank.com for introductions.

The DataTank is free software (AGPL, © 2011,2012 iRail NPO, 2012 OKFN Belgium) to create an API for non-local/dynamic data in no time.

Any questions? Add a support issue.

-Pieter, Jan and Lieven
