<?php

/**
 * Installed definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InstalledDefinition extends SourceType{

    protected $table = 'installeddefinitions';

    protected $fillable = array('path', 'description', 'class');

    /**
     * Validate the input for this model.
     */
    public static function validate($params){
        $validated_params = parent::validate($params);

        // Validate the class after the path has been validated in the parent
        $class_file = app_path() . '/../installed/' .  $params['path'];

        // The file exists through the validation of the parent, but to be sure is checked again here
        if(file_exists($class_file)){
            require_once $class_file;

            $class_name = $params['class'];

            // Check if class exists
            if(!class_exists($class_name)){
                \App::abort('The class file was found, but the class name could not be resolved.');
            }
        }else{
            \App::abort('The class file could not be found with given path: ' . $class_file);
        }

        return $validated_params;
    }

    /**
     * Retrieve the set of create parameters that make up a installed definition.
     */
    public static function getCreateParameters(){
        return array(
            'class' => array(
                'required' => true,
                'name' => 'Class name',
                'description' => 'The name of the class',
                'type' => 'string',
            ),
            'path' => array(
                'required' => true,
                'name' => 'Class file path',
                'description' => 'The location of the class file, relative from the "/installed" folder.',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            )
        );
    }

    /**
     * Retrieve the set of create parameters that make up an installed definition.
     * Include the parameters that make up relationships with this model.
     */
    public static function getAllParameters(){
        return self::getCreateParameters();
    }

    /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'class' => 'required',
            'path' => 'installed|required',
            'description' => 'required',
        );
    }
}
