# model

This folder represents the entire resource model that is being used throughout the framework. In the following sections every important class will be explained.

## ResourcesModel

This is one of the most important classes in the entire framework. It's mainly being used by the Controllers who access this class in order to complete requests (PUT, PATCH, DELETE, GET). The ResourcesModel class represents the entire resourcesmodel, meaning it provides functionality to ask, alter, create and delete resources. It mainly uses factories (Core, Installed,Ontology, GenericResource -factory ) to handle all of its requests. To alter the back-end it uses DBQueries.class.php, which contains all of the SQL statements done to the back-end. A third major interest of the ResourcesModel is the Doc.class.php. The Doc class contains the documentation of EVERYTHING IN THE UNIVERSE..... The DataTank universe that is ofcourse. Everytime something changes in the back-end through the ResourcesModel, it will notify the Doc class to update its documentation, keeping our documentation up to date 24/7.

## AResourceFactory

AResourceFactory is part of the Factory-pattern we use in order to create other specific factories for our types of resources (generic, core, installed and remote). 

The class provides a set of abstract functions that every factory should implement in order to pass over control to other business logic.

### createReader(), -Deleter(), -Creator()

These functions are meant to provide (return) the correct reader, deleter and creator classes. GenericResourceFactory for example will return a GenericReader, GenericDeleter and a GenericCreator. Those classes will be handed over control to insure the further handling of a certain call to The DataTank.

### makeDoc

This function is meant to provide documentation of all of the resources of which the factory is responsible for. For a GenericResourceFactory this will consist of all of the generic resources! The purpose is to only display the documentation string of each of the resources! A more extended version of documentation is to be expected in the makeDescriptionDoc funcion.

### makeDescriptionDoc

This function is meant to provide ALL of the properties that a resource definition exists of. This also includes the documentation string! The entries with all of the properties will be used to serve as a full description of the definition in the TDTAdmin/Resources -resource. This is a private resource, meaning you'll have to have authorization in order to gain access to these descriptions. We've done this because there might be certain uri's in certain resources who have API-keys in them, or other information not suitable for the public.

## GenericResourceFactory (inherits from AResourceFactory)

Implements all of the prescribed functions in the context of generic resources, and adds some functionality in order to get all of the existing strategies.

## InstalledResourceFactory (inherits from AResourceFactory)

Implements most of the prescribed functions with reasonable logic in the context of installed resources. Installed resources are resources that are defined in the packages folder and thus have their own business logic and control. Some functions (such as createDeleter and Creator) have no functionality in this context because installed resources cannot be altered or created through our API!

## CoreResourceFactory (inherits from AResourceFactory)

Implements most of the prescribed functions in the context of core resources. Core resources are resources that come out-of-the-box with a DataTank installation such as TDTInfo/Resources and TDTAdmin/Resources. Just like installed resources you cannot alter or create core resources through our API.

## RemoteResourceFactory (inherits from AResourceFactory)

Implements all of the prescribed functions in the context of remote resources. Remote resources will fetch almost all their documentation from the resource they proxy, explaining the extra functions fetchResourceDescription and fetchResourceDocumentation.

## Doc

This class represents the documentation about all of the resources in The DataTank, it consists of a few functions that each provide another documentationcontext. Note that documentation is cached, and will be refreshed periodically or when something changes in The DataTank that must be notified to the documentation.

## DBQueries

This class contains all of the SQL code that is used by various classes throughout the framework to access the back-end. This class is mainly put together because changes to i.e. naming variables would cause a huge shotgun surgery if every SQL-query has to be looked for in the code and asks for the proper adjustments.









