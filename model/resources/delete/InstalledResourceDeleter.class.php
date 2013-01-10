<?php
/**
 * Class to delete an installed resource
 *
 * @package The-Datatank/model/resources/delete
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace delete;

class InstalledResourceDeleter extends ADeleter{

    /**
     * execution method
     */
    public function delete(){

        /**
         * delete bottom up
         */
        DBQueries::deleteInstalledResource($this->package,$this->resource);
        DBQueries::deleteResource($this->package,$this->resource);
        DBQueries::deletePackage($this->package);
    }
}
?>
