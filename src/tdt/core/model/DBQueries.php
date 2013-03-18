<?php

/**
 * This class contains all queries executed by the model
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 * @author Jan Vansteenlandt
 * @author Pieter Colpaert
 */

namespace tdt\core\model;

use RedBean_Facade as R;

class DBQueries {

    /**
     * Gets the associated Resource.id from a generic resource
     */
    static function getAssociatedResourceId($generic_resource_id) {
        return R::getCell(
                        "SELECT resource_id
             FROM generic_resource
             WHERE id = :gen_res_id", array(":gen_res_id" => $generic_resource_id)
        );
    }

    /**
     * Retrieve a package name by its id
     */
    static function getPackageById($package_id) {
        return R::getCell(
                        "SELECT lower(package_name)
             FROM package
             WHERE id = :package_id", array(":package_id" => $package_id)
        );
    }

    /**
     * Retrieve a package's full name by its id
     */
    static function getFullPackageById($package_id) {
        return R::getCell(
                        "SELECT lower(full_package_name)
             FROM package
             WHERE id = :package_id", array(":package_id" => $package_id)
        );
    }

    /**
     * Retrieve a resource by its id
     */
    static function getResourceById($resource_id) {
        return R::getCell(
                        "SELECT lower(resource_name)
             FROM resource
             WHERE id = :resource_id", array(":resource_id" => $resource_id)
        );
    }

    /**
     * Retrieve a specific resource's documentation
     */
    static function getGenericResourceDoc($package, $resource) {
        return R::getRow(
                        "SELECT generic_resource.documentation as doc, creation_timestamp as creation_timestamp,
                        last_update_timestamp as last_update_timestamp, generic_resource.type as type, generic_resource.id as id
                 FROM package,generic_resource,resource
                 WHERE lower(package.full_package_name)=:package and lower(resource.resource_name) =:resource
                       and package.id=resource.package_id and resource.id = generic_resource.resource_id", array(':package' => $package, ':resource' => $resource)
        );
    }

    /**
     * Get a specific generic resource's type
     */
    static function getGenericResourceType($package, $resource) {
        return R::getRow(
                        "SELECT generic_resource.type as type
             FROM   package,generic_resource,resource
             WHERE lower(package.full_package_name)=:package and lower(resource.resource_name)=:resource
                   and resource_id = resource.id
                   and package.id= resource.package_id", array(':package' => $package, ':resource' => $resource)
        );
    }

    /**
     * Retrieve all generic resources names and their package name
     */
    static function getAllGenericResourceNames() {
        return R::getAll(
                        "SELECT lower(parent_package) as parent, lower(resource.resource_name) as res_name, lower(package.full_package_name) as package_name
             FROM   package,generic_resource,resource
             WHERE  resource.package_id=package.id and generic_resource.resource_id=resource.id"
        );
    }

    /**
     * retrieve package_name and parent_id given a packageId
     */
    static function getPackageAndParentById($package_id) {
        return R::getRow(
                        "SELECT lower(package_name),lower(parent_package) as parent
             FROM package
             WHERE id = :package_id", array(":package_id" => $package_id)
        );
    }

    /**
     * Retrieve all packages
     */
    static function getAllPackages() {
        $results = R::getAll(
                        "SELECT lower(full_package_name) as package_name, timestamp
             FROM package"
        );

        $packages = array();
        foreach ($results as $result) {
            $package = new \stdClass();
            $package->package_name = $result["package_name"];
            $package->timestamp = (int) $result["timestamp"];
            array_push($packages, $package);
        }

        return $packages;
    }

    /**
     * Check if a specific package has a specific resource
     */
    static function hasGenericResource($package, $resource) {
        return R::getRow(
                        "SELECT count(1) as present
             FROM package,generic_resource,resource
             WHERE lower(package.full_package_name)=:package and lower(resource.resource_name)=:resource
             and resource.package_id=package.id and generic_resource.resource_id=resource.id", array(':package' => $package, ':resource' => $resource)
        );
    }

    /**
     * Store a generic resource
     */
    static function storeGenericResource($resource_id, $type, $documentation) {
        R::setStrictTyping(false);
        $genres = R::dispense("generic_resource");
        $genres->resource_id = $resource_id;
        $genres->type = $type;
        $genres->documentation = $documentation;
        $genres->timestamp = time();
        return R::store($genres);
    }

    /**
     * Delete a specific generic resource
     */
    static function deleteGenericResource($package, $resource) {
        return R::exec(
                        "DELETE FROM generic_resource
                      WHERE resource_id IN
                        (SELECT resource.id
                         FROM package,resource
                         WHERE lower(package.full_package_name)=:package and resource.id = generic_resource.resource_id
                         and lower(resource.resource_name) =:resource and resource.package_id = package.id)", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Delete a strategy entry
     */
    static function deleteStrategy($package,$resource,$table){
        return R::exec(
                        "DELETE FROM $table
                    WHERE gen_resource_id IN
                          (SELECT generic_resource.id FROM generic_resource,package,resource
                           WHERE lower(resource.resource_name)=:resource
                                 and lower(package.package_name)=:package
                                 and resource_id = resource.id
                                 and package.id=package_id)", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Retrieve a specific remote resource
     */
    static function getRemoteResource($package, $resource) {
        return R::getRow(
                        "SELECT rem_rec.base_url as url ,lower(rem_rec.package_name) as package,
                    lower(rem_rec.resource_name) as resource
             FROM   package,remote_resource as rem_rec,resource
             WHERE  lower(package.full_package_name)=:package and lower(resource.resource_name) =:resource
                    and package.id = package_id and resource_id = resource.id", array(':package' => $package, ':resource' => $resource)
        );
    }

    /**
     * Get all remote resource names with their package name
     */
    static function getAllRemoteResourceNames() {
        return R::getAll(
                        "SELECT lower(resource.resource_name) as res_name, lower(package.full_package_name) as package_name
              FROM package,remote_resource,resource
              WHERE resource.package_id=package.id
                    and remote_resource.resource_id=resource.id"
        );
    }

    /**
     * Store a remote resource
     */
    static function storeRemoteResource($resource_id, $package_name, $resource_name, $base_url) {
        R::setStrictTyping(false);
        $remres = R::dispense("remote_resource");
        $remres->resource_id = $resource_id;
        $remres->package_name = strtolower($package_name);
        $remres->resource_name = strtolower($resource_name);
        $remres->base_url = $base_url;
        return R::store($remres);
    }

    /**
     * Deletes all remote resources from a specific package
     */
    static function deleteRemotePackage($package) {
        return R::exec(
                        "DELETE FROM remote_resource
                    WHERE package_id IN
                                    (SELECT package.id
                                     FROM package,resource
                                     WHERE lower(package.full_package_name)=:package
                                     and resource_id = resource.id
                                     and package_id = package.id)", array(":package" => $package)
        );
    }

    /**
     * Deletes a specific remote resource
     */
    static function deleteRemoteResource($package, $resource) {
        return R::exec(
                        "DELETE FROM remote_resource
                    WHERE resource_id IN (SELECT resource.id
                                   FROM package,resource
                                   WHERE lower(package.full_package_name)=:package and package_id = package.id
                                   and resource_id = resource.id and lower(resource.resource_name) =:resource
                                   )", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Retrieve a specific package's is
     */
    static function getPackageId($package) {
        return R::getRow(
                        "SELECT package.id as id
             FROM package
             WHERE lower(full_package_name)=:package_name", array(":package_name" => $package)
        );
    }

    /*
     * Retrieve a package's id by it's name and parent id
     */

    static function getPackageIdByParentId($package_name, $parentId) {
        return R::getAll(
                        "SELECT package.id as id
                 FROM package
                 WHERE lower(package_name)=:package_name AND parent_package = :parent_package", array(":package_name" => $package_name, ":parent_package" => $parentId)
        );
    }

    /**
     * Store a package
     */
    static function storePackage($package, $fullPackageName, $parentId) {
        $newpackage = R::dispense("package");
        $newpackage->package_name = strtolower($package);
        $newpackage->timestamp = time();
        $newpackage->parent_package = $parentId;
        $newpackage->full_package_name = strtolower($fullPackageName);
        return R::store($newpackage);
    }

    /**
     * Delete all resources from a package
     */
    static function deletePackageResources($package) {
        return R::exec(
                        "DELETE FROM resource
                    WHERE package_id IN
                    (SELECT id FROM package WHERE lower(full_package_name)=:package)", array(":package" => $package)
        );
    }

    /**
     * Delete a specific package
     */
    static function deletePackage($package) {
        return R::exec(
                        "DELETE FROM package WHERE lower(full_package_name)=:package", array(":package" => $package)
        );
    }

    /**
     * Retrieve a specific resource's id
     */
    static function getResourceId($package_id, $resource) {
        return R::getRow(
                        "SELECT resource.id
             FROM resource, package
             WHERE :package_id = package.id and lower(resource_name) =:resource and package_id = package.id", array(":package_id" => $package_id, ":resource" => $resource)
        );
    }

    /**
     * Retrieve a specific generic resource id
     */
    static function getGenericResourceId($package, $resource) {
        return R::getRow(
                        "SELECT generic_resource.id as gen_resource_id
             FROM package,resource,generic_resource
             WHERE package.id = package_id and lower(full_package_name) =:package and lower(resource_name) =:resource and resource.id = resource_id", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Get the creation timestamp from a resource
     */
    static function getCreationTime($package, $resource) {

        $timestamp = R::getCell(
                        "SELECT resource.creation_timestamp as timestamp
             FROM package,resource
             WHERE package.id = resource.package_id and lower(full_package_name)=:package and lower(resource_name)=:resource", array(":package" => $package, ":resource" => $resource)
        );
        if (!$timestamp) {
            return 0;
        }
        return (int) $timestamp;
    }

    /**
     * Get the creation timestamp from a package
     */
    static function getPackageCreationTime($package) {
        $timestamp = R::getCell(
                        "SELECT timestamp
             FROM  package
             WHERE lower(full_package_name) =:package", array(":package" => $package)
        );
        if (!$timestamp) {
            return 0;
        }
        return (int) $timestamp;
    }

    /**
     * Get the modification timestamp from a resource
     */
    static function getModificationTime($package, $resource) {

        $timestamp = R::getCell(
                        "SELECT resource.last_update_timestamp as timestamp
             FROM package,resource
             WHERE package.id = resource.package_id and lower(full_package_name)=:package and lower(resource_name)=:resource", array(":package" => $package, ":resource" => $resource)
        );
        if (!$timestamp) {
            return 0;
        }
        return (int) $timestamp;
    }

    /**
     * Store a resource
     */
    static function storeResource($package_id, $resource_name, $type) {
        $newResource = R::dispense("resource");
        $newResource->package_id = $package_id;
        $newResource->resource_name = strtolower($resource_name);
        $newResource->creation_timestamp = time();
        $newResource->last_update_timestamp = time();
        $newResource->type = $type;
        return R::store($newResource);
    }

    /**
     * Delete a specific resource
     */
    static function deleteResource($package, $resource) {
        return R::exec(
                        "DELETE FROM resource
             WHERE resource.resource_name=:resource and package_id IN
                   (SELECT id FROM package WHERE lower(full_package_name)=:package)", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Get all published columns of a generic resource
     */
    static function getPublishedColumns($generic_resource_id) {
        return R::getAll(
                        "SELECT column_name, is_primary_key,column_name_alias,published_columns.index
             FROM  published_columns
             WHERE generic_resource_id=:id", array(":id" => $generic_resource_id)
        );
    }

    /**
     * Store a published column
     */
    static function storePublishedColumn($generic_resource_id, $index, $column_name, $column_alias, $is_primary_key) {
        R::setStrictTyping(false);
        $db_columns = R::dispense("published_columns");
        $db_columns->generic_resource_id = $generic_resource_id;
        $db_columns->index = $index;
        $db_columns->column_name = $column_name;
        $db_columns->is_primary_key = $is_primary_key;
        $db_columns->column_name_alias = $column_alias;
        return R::store($db_columns);
    }

    /**
     * Delete published columns for a certain generic resource
     */
    static function deletePublishedColumns($package, $resource) {
        return R::exec(
                        "DELETE FROM published_columns
                    WHERE generic_resource_id IN
                    (
                      SELECT generic_resource.id
                      FROM   generic_resource,resource,package
                      WHERE  lower(full_package_name) = :package
                             and package_id = package.id
                             and lower(resource_name) = :resource
                             and resource_id = resource.id
                    )", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Get the type of the resource
     */
    static function getResourceType($package, $resource) {
        return R::getCell(
                        "SELECT type
             FROM resource,package
             WHERE lower(full_package_name) =:package AND lower(resource_name)=:resource", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Get all the properties of a strategy
     */
    static function getStrategyProperties($generic_resource_id, $strategy_table) {
        return R::getAll(
                        "SELECT *
             FROM $strategy_table
             WHERE gen_resource_id=:gen_res_id", array(":gen_res_id" => $generic_resource_id)
        );
    }

    /**
     * Store the metadata provided
     * @param $resource_id int The resource id of the resource
     * @param $resource object The resource object ( in which the meta data is set )
     * @param $metadata array The array in which all allowed meta data properties are contained.
     */
    static function storeMetaData($resource_id, $resource, $metadataArray) {
        $metadata = R::dispense("metadata");
        $add = false;
        foreach ($metadataArray as $key) {
            if (isset($resource->$key) && $resource->$key != "") {
                $metadata->$key = $resource->$key;
                $add = true;
            }
        }
        $metadata->resource_id = $resource_id;
        if ($add) {
            return R::store($metadata);
        }
    }

    /**
     * Get the metadata for a certain package resource pair
     */
    static function getMetaData($package, $resource) {
        return R::getRow(
                        "SELECT *
             FROM metadata
             WHERE resource_id IN (
                       SELECT resource.id
                       FROM resource,package
                       WHERE lower(resource.resource_name) = :resource AND lower(package.full_package_name) = :package
             )", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Delete the metadata for a certain package resource pair
     */
    static function deleteMetaData($package, $resource) {
        return R::exec(
                        "DELETE
             FROM metadata
             WHERE resource_id IN (
                       SELECT resource.id
                       FROM resource,package
                       WHERE lower(resource.resource_name) = :resource AND lower(package.full_package_name) = :package
             )", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Get all subpackages from a given package
     */
    static function getAllSubpackages($parentId) {
        return R::getAll(
                        "SELECT lower(full_package_name), id
             FROM package
             WHERE parent_package = :parent_id", array(":parent_id" => $parentId)
        );
    }

    /**
     * Store the installed resource
     */
    static function storeInstalledResource($resource_id, $location, $classname) {
        R::setStrictTyping(false);
        $installed_resource = R::dispense("installed_resource");
        $installed_resource->resource_id = $resource_id;
        $installed_resource->location = $location;
        $installed_resource->classname = $classname;
        return R::store($installed_resource);
    }

    /**
     * Delete the installed resource
     */
    static function deleteInstalledResource($package, $resource) {
        return R::exec(
                        "DELETE FROM installed_resource
                    WHERE resource_id IN (SELECT resource.id
                                   FROM package,resource
                                   WHERE lower(package.full_package_name)=:package and package_id = package.id
                                   and resource_id = resource.id and lower(resource.resource_name) =:resource
                                   )", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Get all of the installed resources
     */
    static function getAllInstalledResources() {
        return R::getAll(
                        "SELECT lower(full_package_name) as package, lower(resource_name) as resource
             FROM package,resource,installed_resource
             WHERE resource.package_id=package.id
                    and installed_resource.resource_id=resource.id"
        );
    }

    /**
     * Get the physical location of an installed resource
     */
    static function getLocationOfResource($package, $resource) {
        return R::getCell(
                        "SELECT location
             FROM package,resource,installed_resource
             WHERE resource.package_id=package.id and lower(package.full_package_name) = :package
                    and installed_resource.resource_id=resource.id and lower(resource.resource_name) =:resource", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Get the classname of the installed resource
     */
    static function getClassnameOfResource($package, $resource) {
        return R::getCell(
                        "SELECT classname
             FROM package,resource,installed_resource
             WHERE resource.package_id=package.id and lower(package.full_package_name) = :package
                    and installed_resource.resource_id=resource.id and lower(resource.resource_name) =:resource", array(":package" => $package, ":resource" => $resource)
        );
    }

    /**
     * Ask if a resource is an installed resource
     */
    static function hasInstalledResource($package, $resource) {
        return R::getRow(
                        "SELECT count(1) as present
             FROM package,installed_resource,resource
             WHERE lower(package.full_package_name)=:package and lower(resource.resource_name)=:resource
             and resource.package_id=package.id", array(':package' => $package, ':resource' => $resource)
        );
    }

    /**
     * Get the example uri of resource
     */
    static function getExampleUri($package, $resource) {
        return R::getCell(
                        "SELECT example_uri
             FROM metadata,resource,package
             WHERE lower(package.full_package_name)=:package and lower(resource.resource_name)=:resource
             and resource.package_id=package.id and metadata.resource_id = resource.id", array(":package" => $package, ":resource" => $resource)
        );
    }

    /*
     * Get the resource_id
     * @param resource_name package_id
     */

    static function getResourceIdByPackageId($resource, $package_id) {
        return R::getAll(
                        "SELECT resource.id
             FROM resource, package
             WHERE :package_id = package.id and lower(resource.resource_name) =:resource and resource.package_id = package.id", array(":package_id" => $package_id, ":resource" => $resource)
        );
    }
    
    /*
     * Get the graph URI
     * @param resource_name package_id
     */

    static function getLatestGraph($graph) {
        return R::getCell(
                        "SELECT graph_id
             FROM graph
             WHERE :graph_name = graph_name ORDER BY version DESC LIMIT 1", array(":graph_name" => $graph)
        );
    }
    
    static function getAllGraphs() {
        return R::getAll(
                "SELECT x.graph_id
            FROM graph x
JOIN (
	SELECT graph_name, MAX(version) as version
        FROM graph
	GROUP BY graph_name
) y ON x.graph_name = y.graph_name AND x.version = y.version"
                );
        
    }

}
