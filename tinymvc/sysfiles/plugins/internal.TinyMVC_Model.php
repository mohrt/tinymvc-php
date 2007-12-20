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
 * TinyMVC_Model
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TinyMVC_Model
{


 	/**
	 * $db
	 *
	 * the database object instance
	 *
	 * @access	public
	 */
  var $db = null;  
    
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct() {

    /* load config information */
    include(TMVC_MYAPPDIR . 'configs' . DS . 'database.php');  

    if(!empty($config['plugin']))
    {
      $filename = 'db.' . $config['plugin'] . '.php';
      
      /* look for the plugin in apps/myfiles/sysfiles plugins dirs */
      $filepath = TMVC_MYAPPDIR . 'plugins' . DS . $filename;
      if(!file_exists($filepath))
        $filepath = TMVC_BASEDIR . 'myfiles' . DS . 'plugins' . DS . $filename;
      if(!file_exists($filepath))
        $filepath = TMVC_BASEDIR . 'sysfiles' . DS . 'plugins' . DS . $filename;
      
      if(!file_exists($filepath))
        trigger_error("Unknown database library '{$config['plugin']}'",E_USER_ERROR);
      
      require_once($filepath);

      /* classname must match the plugin name */      
      if(!class_exists($config['plugin']))
        trigger_error("Unknown database class '{$config['plugin']}'",E_USER_ERROR);
      
      /* assign the object instance as a property */
      $this->db = new $config['plugin'];
    }

  }
  
}

?>
