# tdt/core


[![Latest Stable Version](https://poser.pugx.org/tdt/core/version.png)](https://packagist.org/packages/tdt/core)
[![Build Status](https://travis-ci.org/tdt/core.png?branch=development)](https://travis-ci.org/tdt/core) [![Dependency Status](https://www.versioneye.com/php/tdt:core/badge.png)](https://www.versioneye.com/php/tdt:core)

The DataTank core is the framework in which the main application of The DataTank is built. The DataTank aims at publishing data to URI's in web readable formats. This means that you provide a nice JSON, XML, PHP, ... serialization on a certain URI from which the data resides somewhere in a CSV, XLS, XML, JSON, SHP, ... file.

# Read more

If you want to read more about The DataTank project visit our [website](http://thedatatank.com) or take a look at our [documentation](http://docs.thedatatank.com).

The DataTank is free software (AGPL, © 2011,2012 iRail NPO, 2012 OKFN Belgium) to create an API for non-local/dynamic data in no time.

Any questions? Add a support issue.

## Setting up using Vagrant?

First install vagrant if you didn't do so yet, then perform:
```bash
vagrant up
```
In the root of this git repository. It may take a while before your virtual machine running The DataTank is ready.

You will need to add a new entry to your hosts. On unix systems this works as follows:
 * edit /etc/hosts
 * add the line `172.23.5.42    tdt.dev`

