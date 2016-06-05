# tdt/core

[![Join the chat at https://gitter.im/tdt/core](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/tdt/core?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)


[![Latest Stable Version](https://poser.pugx.org/tdt/core/version.png)](https://packagist.org/packages/tdt/core)
[![Build Status](https://travis-ci.org/tdt/core.svg?branch=development)](https://travis-ci.org/tdt/core) [![Dependency Status](https://www.versioneye.com/php/tdt:core/badge.png)](https://www.versioneye.com/php/tdt:core)

The DataTank core is the framework in which the main application of The DataTank is built. The DataTank is a web application that publishes data to URIs in web readable formats. This means that you provide a nice JSON, XML, PHP, ... serialization on a certain URI of data that resides somewhere in a CSV, XLS, XML, JSON, SHP, ... file or any other machine readable data container. In short, it provides an instant REST API on top of any machine readable data.

## Installation

[Installation docs](http://docs.thedatatank.com/5.12/installation)

## Front end

Run `npm install` when upgrading to 6.6 

`gulp` will build all static assets for production  
`gulp serve` will watch `*.js` & `*.scss`  
`gulp js` will minify the jQuery source  
`gulp webpack` will build the Vue project (currently only for homepage)

# Read more

If you want to read more about The DataTank project visit our [website](http://thedatatank.com) or take a look at our [documentation](http://docs.thedatatank.com).

The DataTank is free software (AGPL, © 2011,2012 iRail NPO, 2012 Open Knowledge Belgium) to create an API for data in no time.

Any questions? Add a support issue!
