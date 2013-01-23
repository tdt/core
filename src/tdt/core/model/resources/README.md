# Resource package

There are 4 different resources in The DataTank:
 * Remote Resource - These resources are hosted on another DataTank instance. You can use your server as a proxy to access this data if those would be of your interest.
 * Generic Resource - A generic resource is a resource that handles certain generic datasources. These generic resources become concrete with the addition of a strategy to that generic resource. For example a CSV file is something that can be accessed in a generic way. This will result in a generic resource with a strategy called CSV, resulting in a resource that can handle any CSV file.
 * Installed Resource - In other cases the datasource is not structured at all and we need to write a scraper for instance. You can add you own Resource by implementing AResource.class.php yourself and putting your Resource in the modules/???/ dir
 * Core Resource - same thing as Installed resource, although they're in a different logical location: model/packages/$package/$resource.class.php

Note that sometimes you might see documentation where we put out that there are 3 types of resources. This is because in theory core resources are a subset of installed resources, but with the context difference that core resources are pre-installed by The DataTank itself.

For each type of resource we have 5 actions:
 * create - Creates a new resource with the specified parameters or overwrite it
 * read - Reads information from a certain resource
 * patch - Updates data or adds metadata to the resource
 * delete - Deletes a certain resource and all its dependencies
 * head - Does exactly the same as a read, but returns only the headers of the request without the body.

## Folder structure

For every operation, there are folders provided in which the appropriate classes can be found for CRUD purposes.