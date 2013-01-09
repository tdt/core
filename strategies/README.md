# Strategies directory

This directory contains all the strategies that can be used in The DataTank. These strategies are a part of the GenericResource class, found in the folder model/resources. All of these strategies inherit from AResourceStrategy.class.php. If you want to create a new strategy, all you have to do is overwrite some of the functions from the AResourceStrategy class in order to get your own logic to work. 

## Creating your own strategy

This section explains how to build your own strategy from scratch. To make this somewhat easier for the developer we'll use an existing strategy, namely the CSV-strategy throughout this small tutorial. This allows us to explain an abstract class we provided in this folder called ATabularData.class.php. This class provides some extra functionality if your resource, for which you are building a strategy for, is of a tabular format such as CSV or XLS.

* How to get started: Parameters

The very first thing the creator of a strategy must know is what parameters it expects in order to read the datasource. These must be put in the function documentCreateRequiredParameters():

    // example from CSV
    public function documentCreateRequiredParameters(){
           return array("uri");               
    }

This will tell the framework that if someone wants to add a CSV resource they will have to pass uri for addition of the resource to work. The above function is used internally to gain documentation about the resource's necessary parameters. To let users know what parameters can be passed and what their purpose is, you have to fill in the function documentCreateParameters():

     // example from CSV
     public function documentCreateParameters(){
            $this->parameters["uri"] = "The URI to the CSV file.";
            $this->parameters["PK"] =  "The primary key of an entry. This must be the name of an existing column name in the CSV file."; 
            $this->parameters["has_header_row"] = "If the CSV file contains a header row with the column name, pass 1 as value, if not pass 0. Default value is 1.";
            $this->parameters["delimiter"] = "The delimiter which is used to separate the fields that contain values, default value is a comma.";
            $this->parameters["start_row"] = "The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.";
            return $this->parameters;
     }

Note that parameters is a protected datamember inherited from ATabularData.class.php, in strategies that don't inherit from ATabularData you can return an array with the same information with the name of the parameters mapped onto their documentation.

As you can see uri is passed again, along with some other parameters. This way you tell the framework you don't mind if users don't pass parameters next to uri. In the case of the CSV-strategy we will make default assumptions for those parameters. For example in a CSV file, mostly the delimiter will be a comma, so when a user does not pass delimiter, we will use the default value. This is ofcourse documented in the documentation string in the array returned in the documentCreateParameters function. We thus encourage developers of strategies to document their parameters as good as possible.

Once this is done, you will have access to these parameters in the read(&$configObject) function, which will be explained later in this section.

* How to get started: validation

Passing the correct parameters is just one part of the preparation to create an instance of a strategy. We also provided the possibility to allow validation before you finish the addition of a strategy instance. If you do not overwrite the isValid() function, it will by default return true. In some cases ( for files from which you are 100% sure they are correct and consistent ) you might not need to overwrite this, but in general we'll want to check if the datasource file ( CSV in our little tutorial case ) is a correct one. In our CSV strategy we get the CSV file and take a look inside and do some validation stuff such as checking if the amount of header columns equals the amount of data columns.

* How to get started: additional information for tabular resources

The above information is applicable to strategies in general. If you have a tabular datasource, you'll have to deal with another default parameter called columns.

The framework expects that every tabular resource has columns, and that these columns parameter is filled in the addition of the strategy instance. This means that if a user does not pass along columns as a parameter, the developer of the strategy will have to do this in code. A perfect example can be found in the CSV file. In the CSV file if the columns are not passed, they will be filled in while validating the CSV file.

* How to get started: Reading

Once a user has passed along the necessary parameters and the logic in the isValid() function has turned out positive, a user can access its resource it has just made via a GET HTTP request. The data the user will get to see is a representation of the object the developer creates in the read(&$configObject) function. In the configObject you can access all the parameters you have declared in the parameter functions ( see above ). In the CSV-strategy we use all of these parameters to read a CSV file properly and build an object containing the information of the CSV-file.

In the sections below every strategy is explained in terms of what datasources it handles, what create/read parameters it expects and some remarks.

## ATabularData

ATabularData is an abstract class that inherits from AResourceStrategy. It provides some functionality that comes in handy to create tabular specific strategies. For example, when you have validated your datasource, it will automatically ask for the columns parameter, and store them in a correct way in the back-end, making it so that even though tabular strategies require somewhat more functionality, the developer isn't much aware of it and can develop its strategy just like any normal (non-tabular) strategy for the most part.

### required create parameters

none

### additional create parameters

* columns : An array that contains the name of the columns that are to be published, if an empty array is passed every column will be published. Note that this parameter is not required, however if you do not have a header row, we do expect the columns to be passed along, otherwise there's no telling what the names of the columns are. This array should be build as column_name => column_alias or index => column_alias.

### remarks

Even though it is a non-required parameter towards the user who wants to add a strategy instance, it still expects a columns parameter to be passed. Thus the developer has to make sure that if no columns have been passed from a user, it has to create the and pass along the columns parameter itself. In the CSV strategy this is done in the isValid() function.

## CSV (inherits from ATabularData)

This class represents a strategy that handles CSV datasources.

### required create parameters:

* uri: The URI to the CSV file.

### additional create parameters

* PK : The primary key of an entry. This must be the name of an existing column name in the CSV file.
* has_header_row : If the CSV file contains a header row with the column name, pass 1 as value, if not pass 0. Default value is 1.
* delimiter : The delimiter which is used to separate the fields that contain values, default value is a comma.
* start_row : The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.
 
### validation

The validation function will check if the number of header columns equals the number of data columns, taking in consideration trailing empty cells! The validation will also create columns from the file if no columns have been passed, AND the has_header_row has been set, otherwise it will throw an exception stating columns have not been passed.

### remarks:

PK is something unique in each row, every row will be mapped onto this unique key. If there are however multiple row with the same unique key, only the first will be used, the rest will be thrown away. ( From now on every PK parameter is defined in this way, unless declared otherwise.)

## JSON

This class represents a strategy that handles JSON datasources.

### required create parameters:

* uri : The uri to the json document.

### additional create parameters

none !

### validation

The validation exists of a json_decode of the json file, if it succeeds, the validation succeeds.

## KML (inherits from ATabularData)

This class represents a strategy that handles KML datasources.

### required create parameters:

* uri : The uri to the KML file

### additional create parameters

* columns : The columns that are to be published from the KML.
* PK : The primary key of each row.

### validation

The validation checks if all the KML specific entities are present, if so, the validation will succeed.

### read parameters ( parameters you pass along in the GET request )

* long : longitude
* lat : latitude
* radius : radius (km metric)

Adding these parameters will filter the data returned to entries that are within the area specified with long, lat and radius.

## GeoJSON (inherits from ATabularData)

This class represents a strategy that handles JSON files that have a specific geo-structure.

### required create parameters

* uri : The uri to the geo-JSON file.

### additional create parameters

* columns : The columns that are to be published from the JSON file.
* PK : The primary key of each row.

### validation

The validation consists of checking if it's a valid geo-JSON file.

### read parameters ( parameters you pass along in the GET request )

* long : longitude
* lat : latitude
* radius : radius (km metric)

Adding these parameters will filter the data returned to entries that are within the area specified with long, lat and radius.

## SHP (inherits from ATabularData)

This class represents a strategy that handles Shape files.

### required create parameters

* uri : The path to the shape file.

### additional create parameters

* EPSG : EPSG coordinate system code.
* columns : The columns that are to be published.
* PK : The primary key for each row.

### validation

Checks if all necessary files (next to .shp) are present in the zip file, just like the zipped shape structure describes. 

### remarks

For zipped shape files, use the ZippedSHP resource, both ZippedSHP and SHP use the temp folder to temporarily store the SHP files.

## ZippedSHP (inherits from SHP)

This class represents a strategy that handles zipped shape files.

### required create parameters

* uri : The path to the zipped shape file.
* shppath : The path to the shape file within the zip.

### additional create parameters

* EPSG : EPSG coordinate system code.
* columns : The columns that are to be published.
* PK : The primary key for each row.

### validation

Same as in SHP.

### remarks

Also uses the temp folder to temporarily store the shp files, just like SHP strategy does.

## XLS (inherits from ATabularData)

This class represents a strategy that handles XLS files.

### required create parameters

* uri : The path to the excel sheet (can be a url as well).
* sheet : The sheet name of the excel of which you want to extract data.

### additional create parameters

* named_range : The named range of the excel.
* cell_range : Range of cells (i.e. A1:B10).
* PK : The primary key for each row.
* has_header_row : If the XLS file contains a header row with the column name, pass 1 as value, if not pass 0. Default value is 1.
* start_row : The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.

### validation

Somewhat similar to the CSV file, will check if the XLS file exists, and if necessary form the columns parameter during the validation.

### remarks

The XLS files will be locally stored because they cannot be streamed. Therefore we use the tmp file in the The DataTank framework folder structure.

## XML

This class represents a strategy that handles XML files. Note that currently there is no support for namespaced XML files (yet).

### required create parameters

* uri : The uri to the XML file.

### additional create parameters

none

## DB

This class represents a strategy that handles database datasources.

### required create parameters:

We use the doctrine 2 DBAL to access the database resources, so our support for databases is dependent on the support of the doctrine 2 DBAL which are:

* mysql
* sqlite
* pgsql
* sqlsrv
* oci8 ( An Oracle driver that uses the oci8 PHP extension.) 

Necessary parameters for SQLite database dataresources:

* db_type  : sqlite
* location : The location of the SQLite database 
* db_table : The database table of which some or all fields will be published.

Necessary parameters for non-SQLite database dataresources:

* username : The username to connect to the database with.
* password : The password of the user to connect to the database.
* db_name  : The database name.
* db_type  : The type of the database, current supported types are: mysql,pgsql,oci8,sqlsrv
* db_table : The database table of which some or all fields will be published.
* location : The location of the database this is the host on which the database is running.

### additional create parameters

* port     : The port number to connect to. (not for SQLite based DB resources.)
* PK : The primary key of an entry. This must be the name of an existing column name in the tabular resource.


### validation

A check is done to the database to get the columnnames. If this fails, then we a wrong set of parameters has been passed. Also we check, when columns are passed with the request, if they are existing columns.

