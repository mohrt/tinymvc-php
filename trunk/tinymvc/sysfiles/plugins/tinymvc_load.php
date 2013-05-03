<?php

/**
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007-2008 Monte Ohrt, All rights reserved.
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
	 * @param   string $pool_name the database pool name to use
	 * @return  boolean
	 */    
  public function model($model_name,$model_alias=null,$filename=null,$pool_name=null)
  {

    /* if no alias, use the model name */
    if(!isset($model_alias))
      $model_alias = $model_name;

    /* if no filename, use the lower-case model name */
    if(!isset($filename))
      $filename = strtolower($model_name) . '.php';

    if(empty($model_alias))  
      throw new Exception("Model name cannot be empty");

    if(!preg_match('!^[a-zA-Z][a-zA-Z0-9_]+$!',$model_alias))
      throw new Exception("Model name '{$model_alias}' is an invalid syntax");
      
    if(method_exists($this,$model_alias))
      throw new Exception("Model name '{$model_alias}' is an invalid (reserved) name");

    /* get instance of controller object */
    $controller = tmvc::instance(null,'controller');
    
    /* model already loaded? silently skip */
    if(isset($controller->$model_alias))
      return true;
    
    /* instantiate the object as a property */
    $controller->$model_alias = new $model_name($pool_name);
    
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
  public function library($lib_name,$alias=null,$filename=null)
  {

    /* if no alias, use the class name */
    if(!isset($alias))
      $alias = $lib_name;

    if(empty($alias))  
      throw new Exception("Library name cannot be empty");

    if(!preg_match('!^[a-zA-Z][a-zA-Z_]+$!',$alias))
      throw new Exception("Library name '{$alias}' is an invalid syntax");
      
    if(method_exists($this,$alias))
      throw new Exception("Library name '{$alias}' is an invalid (reserved) name");
    
    /* get instance of tmvc object */
    $controller = tmvc::instance(null,'controller');    

    /* library already loaded? silently skip */
    if(isset($controller->$alias))
      return true;
    
    $class_name = "TinyMVC_Library_{$lib_name}";
    
    /* instantiate the object as a property */
    $controller->$alias = new $class_name;  
    
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
      throw new Exception("Invalid script name '{$script_name}'");
    
    $filename = strtolower("TinyMVC_Script_{$script_name}.php");

    try {
      require_once($filename);
    } catch (Exception $e) {
      throw new Exception("Unknown script file '{$filename}'");      
    }
      
  }

	/**
	* database
	*
	* returns a database plugin object
	*
	* @access	public
	* @param	string $poolname the name of the database pool (if NULL default pool is used)
	* @return	object
	*/
  public function database($poolname = null) {
    static $dbs = array();
    /* load config information */
    include('config_database.php');
    if(!$poolname) 
      $poolname=isset($config['default_pool']) ? $config['default_pool'] : 'default';
    if ($poolname && isset($dbs[$poolname]))
    {
      /* returns object from runtime cache */
	    return $dbs[$poolname];
    }
    if($poolname && isset($config[$poolname]) && !empty($config[$poolname]['plugin']))
    {
      /* add to runtime cache */
      $dbs[$poolname] = new $config[$poolname]['plugin']($config[$poolname]);
      return $dbs[$poolname];
     }
  }  
  
}

?>
