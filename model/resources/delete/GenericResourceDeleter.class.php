<?php
/**
 * Class to delete a generic resource
 *
 * @package The-Datatank/model/resources/delete
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\delete;

class GenericResourceDeleter extends tdt\core\model\resources\delete\ADeleter{

    /**
     * execution method
     */
    public function delete(){

        $resource = new tdt\core\model\resources\GenericResource($this->package,$this->resource);
        $strategy = $resource->getStrategy();
        $strategy->onDelete($this->package,$this->resource);
            
        // delete any published columns entry
        tdt\core\model\DBQueries::deletePublishedColumns($this->package,$this->resource);
        
        // delete metadata about the resource
        tdt\core\model\DBQueries::deleteMetaData($this->package,$this->resource);

        //now the only thing left to delete is the main row
       tdt\core\model\DBQueries::deleteGenericResource($this->package, $this->resource);

        // also delete the resource entry
        tdt\core\model\DBQueries::deleteResource($this->package,$this->resource);

    }
}
?>