# tdt/core


[![Latest Stable Version](https://poser.pugx.org/tdt/core/version.png)](https://packagist.org/packages/tdt/core)
[![Build Status](https://travis-ci.org/tdt/core.png?branch=development)](https://travis-ci.org/tdt/core) [![Dependency Status](https://www.versioneye.com/php/tdt:core/badge.png)](https://www.versioneye.com/php/tdt:core)

The DataTank core is the framework in which the main application of The DataTank is built. The DataTank aims at publishing data to URI's in web readable formats. This means that you provide a nice JSON, XML, PHP, ... serialization on a certain URI from which the data resides somewhere in a CSV, XLS, XML, JSON, SHP, ... file.

# Read more

If you want to read more about The DataTank project visit our [website](http://thedatatank.com) or take a look at our [documentation](http://docs.thedatatank.com).

The DataTank is free software (AGPL, © 2011,2012 iRail NPO, 2012 OKFN Belgium) to create an API for non-local/dynamic data in no time.

# About this branch

This branch has a small addition to configure and execute RML mappings. This requires you to have the RMLProcessor installed, the project can be found [here](https://github.com/mmlab/RMLProcessor). After you installed the project, configure the home folder in the rml.php config file.

Note: If you're doing a git pull make sure you perform "mvn clean install", stuff might go bad if you don't.

You can then add an RML mapping document as you would configure any other datasource, and then execute the mapping through the artisan command:

php artisan rml:execute {name of identifier}


# Upload to Virtuoso

At the time of writing the RMLProcessor doesn't load any triples into a triple store yet, instead it puts the triples into a file. If you're finding yourself wanting to upload this file into a triple store like Virtuoso, log into the conductor application, head to "Linked Data" -> "Quad Store Upload" -> choose your file with triples and upload it to a graph.

# Addendum: Install Virtuoso from source

## Step 1.

clone the virtuoso repo https://github.com/openlink/virtuoso-opensource

## Step 2.

Follow the instruction steps in the readme of the repo BUT(!!):

export the cflags like it's written in the docs of virtuoso for the 64 bit version and for your Mac OSX version (10.10 in my case) while in the readme
the largest version is 10.7:

CFLAGS="-O -m64 -mmacosx-version-min=10.10"

This will NOT work!! You're very likely to get some sort of heap error somewhere along the line, so you'll need to change something in a source (C) file of the virtuoso:

[link to related issue and solution](https://github.com/openlink/virtuoso-opensource/issues/277)

## Step 4.

So everything is in place:

Changed stack size
Changed flags to 10.10

Hit those commands (./configure, make and make install)

## Step 5.

Boot up virtuoso -> check readme