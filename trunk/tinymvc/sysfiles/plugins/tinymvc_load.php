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

    /* model already loaded? silently skip */
    if(isset($this->$model_alias))
      return true;
      
    $filepath = TMVC_MYAPPDIR . 'models' . DS . $filename;
  
    if(!file_exists($filepath))
      throw new Exception("Unknown model file '{$filename}'");

    require_once($filepath);
    
    /* class name must be the same as the model name */
    if(!class_exists($model_name,false))
      throw new Exception("Unknown classname '{$model_name}'");
    
    /* get instance of controller object */
    $controller = tmvc::instance(null,'controller');
    
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
  public function library($class_name,$alias=null,$filename=null)
  {

    /* if no alias, use the class name */
    if(!isset($alias))
      $alias = $class_name;

    if(empty($alias))  
      throw new Exception("Library name cannot be empty");

    if(!preg_match('!^[a-zA-Z][a-zA-Z_]+$!',$alias))
      throw new Exception("Library name '{$alias}' is an invalid syntax");
      
    if(method_exists($this,$alias))
      throw new Exception("Library name '{$alias}' is an invalid (reserved) name");

    /* library already loaded? silently skip */
    if(isset($this->$alias))
      return true;

    /* if no class exists, attempt to load plugin */
    if(!class_exists($class_name,false))
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
        throw new Exception("Unknown library '{$class_name}'");
  
      require_once($filepath);
      
      if(!class_exists($class_name,false))
        throw new Exception("Unknown classname '{$class_name}'");
    
    }    
    
    /* get instance of tmvc object */
    $controller = tmvc::instance(null,'controller');    
    
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
    
    $filename = 'script.' . $script_name . '.php';

    /* look in myapps/myfiles/sysfiles plugins dirs */
    $filepath = TMVC_MYAPPDIR . 'plugins' . DS . $filename;
    if(!file_exists($filepath))
      $filepath = TMVC_BASEDIR . 'myfiles' . DS . 'plugins' . DS . $filename;
    if(!file_exists($filepath))
      $filepath = TMVC_BASEDIR . 'sysfiles' . DS . 'plugins' . DS . $filename;
  
    if(!file_exists($filepath))
      throw new Exception("Unknown script file '{$filename}'");

    return require_once($filepath);
      
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
    include(TMVC_MYAPPDIR . 'configs' . DS . 'database.php');
    if(!$poolname) 
      $poolname=isset($config['default_pool']) ? $config['default_pool'] : 'default';
    if ($poolname && isset($dbs[$poolname]))
    {
      /* returns object from runtime cache */
	    return $dbs[$poolname];
    }
    if($poolname && isset($config[$poolname]) && !empty($config[$poolname]['plugin']))
    {
      $filename = 'db.' . $config[$poolname]['plugin'] . '.php';
      
      /* look for the plugin in apps/myfiles/sysfiles plugins dirs */
      $filepath = TMVC_MYAPPDIR . 'plugins' . DS . $filename;
      if(!file_exists($filepath))
        $filepath = TMVC_BASEDIR . 'myfiles' . DS . 'plugins' . DS . $filename;
      if(!file_exists($filepath))
        $filepath = TMVC_BASEDIR . 'sysfiles' . DS . 'plugins' . DS . $filename;
      
      if(!file_exists($filepath))
        throw new Exception("Unknown database library '{$config[$poolname]['plugin']}'");
      
      require_once($filepath);

      /* classname must match the plugin name */      
      if(!class_exists($config[$poolname]['plugin'],false))
        throw new Exception("Unknown database class '{$config[$poolname]['plugin']}'");
      /* add to runtime cache */
      $dbs[$poolname] = new $config[$poolname]['plugin']($config[$poolname]);
      return $dbs[$poolname];
     }
  }  
  
}

?>
