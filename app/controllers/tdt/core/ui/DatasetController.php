<?php

/**
 * The datasetcontroller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace tdt\core\ui;

use tdt\core\auth\Auth;

class DatasetController extends \Controller {

    /**
     * Admin.dataset.view
     */
    public function getIndex(){

        // Set permission
        Auth::requirePermissions('admin.dataset.view');

        // Get all definitions
        $definitions = \Definition::all();

        return \View::make('ui.datasets.list')
                    ->with('title', 'The Datatank')
                    ->with('definitions', $definitions);

        return \Response::make($view);
    }

    /**
     * Admin.dataset.update
     */
    public function getEdit($id){

        // Set permission
        Auth::requirePermissions('admin.dataset.update');

        $definition = \Definition::find($id);
        if($definition){

            return \View::make('ui.datasets.edit')
                        ->with('title', 'The Datatank')
                        ->with('definition', $definition);

            return \Response::make($view);

        }else{
            return \Redirect::to('api/admin/datasets');
        }
    }

    /**
     * Admin.dataset.delete
     */
    public function getDelete($id){

        // Set permission
        Auth::requirePermissions('admin.dataset.delete');

        if(is_numeric($id)){
            $definition = \Definition::find($id);
            if($definition){
                // Delete it (with cascade)
                $definition->delete();
            }
        }

        return \Redirect::to('api/admin/datasets');
    }

}