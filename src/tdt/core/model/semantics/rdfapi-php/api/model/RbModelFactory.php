<?php

require_once RDFAPI_INCLUDE_DIR . 'model/RbStore.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RbModelFactory
 *
 * @author mvdrsand
 */
class RbModelFactory extends ModelFactory {

    /**
     * Returns a ResModel.
     * $modelType has to be one of the following constants:
     * MEMMODEL,DBMODEL,INFMODELF,INFMODELB to create a resmodel with a new
     * model from defined type.
     * You can supply a base URI
     *
     * @param   constant  $modelType
     * @param   string  $baseURI
     * @return	object	ResModel
     * @access	public
     */
    public static function getResModel($modelType, $baseURI = null) {
        switch ($modelType) {

            case RBMODEL:
                $baseModel = RbModelFactory::getDefaultRbModel($baseURI);
                break;

            case DBMODEL:
                $baseModel = ModelFactory::getDefaultDbModel($baseURI);
                break;

            case INFMODELF:
                $baseModel = ModelFactory::getInfModelF($baseURI);
                break;

            case INFMODELB:
                $baseModel = ModelFactory::getInfModelB($baseURI);
                break;

            default:
                $baseModel = ModelFactory::getMemModel($baseURI);
                break;
        }
        return ModelFactory::getResModelForBaseModel($baseModel);
    }

    public static function getDefaultRbModel($baseURI = null) {
        $rbStore = RbModelFactory::getRbStore();
        $m = RbModelFactory::getRbModel($rbStore, $baseURI);
        return $m;
    }

    public static function getRbModel($rbStore, $baseURI = null) {
        if ($rbStore->modelExists($baseURI)) {
            return $rbStore->getModel($baseURI);
        }

        return $rbStore->getNewModel($baseURI);
    }

    public static function getRbStore() {
        $dbs = new RbStore();
        return $dbs;
    }

}

?>
