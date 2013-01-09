<?php
/**
 * Class to delete a remote resource
 *
 * @package The-Datatank/model/resources/delete
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\delete;

class RemoteResourceDeleter extends tdt\core\model\resources\delete\ADeleter{

    /**
     * execution method
     */
    public function delete(){

        /**
         * delete bottom up
         */
        tdt\core\model\DBQueries::deleteRemoteResource($this->package,$this->resource);
        tdt\core\model\DBQueries::deleteResource($this->package,$this->resource);
        tdt\core\model\DBQueries::deletePackage($this->package);
    }
}
?>
