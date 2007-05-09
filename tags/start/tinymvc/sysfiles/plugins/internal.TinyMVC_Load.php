<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007, New Digital Group Inc.
 * Author:     Monte Ohrt, monte [at] ohrt [dot] com
 * License:    LGPL, see included license file  
 ***/

// ------------------------------------------------------------------------

/**
 * TinyMVC_Load
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TinyMVC_Load
{
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct() { }

	/**
	 * model
	 *
	 * load a model object
	 *
	 * @access	public
	 * @param   string $model_name the name of the model class
	 * @param   string $model_alias the property name alias
	 * @param   string $filename the filename
	 * @return  boolean
	 */    
  public function model($model_name,$model_alias=null,$filename=null)
  {

    /* if no alias, use the model name */
    if(!isset($model_alias))
      $model_alias = $model_name;

    /* if no filename, use the lower-case model name */
    if(!isset($filename))
      $filename = strtolower($model_name) . '.php';

    if(empty($model_alias))  
      trigger_error("Model name cannot be empty",E_USER_ERROR);

    if(!preg_match('!^[a-zA-Z][a-zA-Z_]+$!',$model_alias))
      trigger_error("Model name '{$model_alias}' is an invalid syntax",E_USER_ERROR);
      
    if(method_exists($this,$model_alias))
      trigger_error("Model name '{$model_alias}' is an invalid (reserved) name",E_USER_ERROR);

    /* model already loaded? silently skip */
    if(isset($this->$model_alias))
      return true;
      
    $filepath = TMVC_MYAPPDIR . 'models' . DS . $filename;
  
    if(!file_exists($filepath))
      trigger_error("Unknown model file '{$filename}'",E_USER_ERROR);

    require_once($filepath);
    
    /* class name must be the same as the model name */
    if(!class_exists($model_name))
      trigger_error("Unknown classname '{$model_name}'",E_USER_ERROR);
    
    /* get instance of tmvc object */
    $tmvc = tmvc::instance();
    
    /* instantiate the object as a property */
    $tmvc->$model_alias = new $model_name;
    
    return true;
      
  }

	/**
	 * library
	 *
	 * load a library plugin
	 *
	 * @access	public
	 * @param   string $class_name the class name
	 * @param   string $alias the property name alias
	 * @param   string $filename the filename
	 * @return  boolean
	 */    
  public function library($class_name,$alias=null,$filename=null)
  {

    /* if no alias, use the class name */
    if(!isset($alias))
      $alias = $class_name;

    if(empty($alias))  
      trigger_error("Library name cannot be empty",E_USER_ERROR);

    if(!preg_match('!^[a-zA-Z][a-zA-Z_]+$!',$alias))
      trigger_error("Library name '{$alias}' is an invalid syntax",E_USER_ERROR);
      
    if(method_exists($this,$alias))
      trigger_error("Library name '{$alias}' is an invalid (reserved) name",E_USER_ERROR);

    /* library already loaded? silently skip */
    if(isset($this->$alias))
      return true;

    /* if no class exists, attempt to load plugin */
    if(!class_exists($class_name))
    {

      /* if no filename, use the class name */
      if(!isset($filename))
        $filename = 'library.' . $class_name . '.php';
  
      /* look in myapps/myfiles/sysfiles plugins dirs */
      $filepath = TMVC_MYAPPDIR . 'plugins' . DS . $filename;
      if(!file_exists($filepath))
        $filepath = TMVC_BASEDIR . 'myfiles' . DS . 'plugins' . DS . $filename;
      if(!file_exists($filepath))
        $filepath = TMVC_BASEDIR . 'sysfiles' . DS . 'plugins' . DS . $filename;
    
      if(!file_exists($filepath))
        trigger_error("Unknown library '{$class_name}'",E_USER_ERROR);
  
      require_once($filepath);
      
      if(!class_exists($class_name))
        trigger_error("Unknown classname '{$class_name}'",E_USER_ERROR);
    
    }    
    
    /* get instance of tmvc object */
    $tmvc = tmvc::instance();    
    
    /* instantiate the object as a property */
    $tmvc->$alias = new $class_name;    
    
    return true;
      
  }

	/**
	 * script
	 *
	 * load a script plugin
	 *
	 * @access	public
	 * @param   string $script_name the script plugin name
	 * @return  boolean
	 */    
  public function script($script_name)
  {

    if(!preg_match('!^[a-zA-Z][a-zA-Z_]+$!',$script_name))
      trigger_error("Invalid script name '{$script_name}'",E_USER_ERROR);
    
    $filename = 'script.' . $script_name . '.php';

    /* look in myapps/myfiles/sysfiles plugins dirs */
    $filepath = TMVC_MYAPPDIR . 'plugins' . DS . $filename;
    if(!file_exists($filepath))
      $filepath = TMVC_BASEDIR . 'myfiles' . DS . 'plugins' . DS . $filename;
    if(!file_exists($filepath))
      $filepath = TMVC_BASEDIR . 'sysfiles' . DS . 'plugins' . DS . $filename;
  
    if(!file_exists($filepath))
      trigger_error("Unknown script file '{$filename}'",E_USER_ERROR);

    return require_once($filepath);
      
  }

}

?>
