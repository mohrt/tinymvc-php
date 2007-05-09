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
 * TinyMVC_Controller
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TinyMVC_Controller
{

 	/**
	 * $view_vars
	 *
	 * vars for view file assignment
	 *
	 * @access	public
	 */
  var $view_vars = array();
  
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct()
  {
    /* set the class instance */
    tmvc::instance($this);
    
    /* instantiate load library */
    $this->load = new TinyMVC_Load;  
  }
  
	/**
	 * index
	 *
	 * the default controller method
	 *
	 * @access	public
	 */    
  function index() { }

	/**
	 * assign
	 *
	 * assign view variables
	 *
	 * @access	public
	 * @param   mixed $key key of assignment, or value to assign
	 * @param   mixed $value value of assignment
	 */    
  public function assign($key, $value=null)
  {
    if(isset($value))
      $this->view_vars[$key] = $value;
    else
      foreach($key as $k => $v)
        if(is_int($k))
          $this->view_vars[] = $v;
        else
          $this->view_vars[$k] = $v;
  }  

	/**
	 * view
	 *
	 * display a view file
	 *
	 * @access	public
	 * @param   string $filename the name of the view file
	 * @return  boolean
	 */    
  public function view($_tmvc_filename)
  {
    return $this->_view(TMVC_MYAPPDIR . 'views' . DS . "{$_tmvc_filename}.php");
  }  

	/**
	 * fetch
	 *
	 * return the contents of a view file
	 *
	 * @access	public
	 * @param   string $filename
	 * @return  string contents of view
	 */    
  public function fetch($filename)
  {
    ob_start();
    $this->view($filename,$view_vars);
    $results = ob_get_contents();
    ob_end_clean();
    return $results;
  }  

	/**
	 * _sysview
	 *
	 * internal: view a system file
	 *
	 * @access	private
	 * @param   string $filename
	 * @return  boolean
	 */    
  public function _sysview($filename)
  {
    $filepath = TMVC_BASEDIR . 'sysfiles' . DS . 'views' . DS . "{$filename}.php";
    return $this->_view($filepath);
  }

	/**
	 * _view
	 *
	 * internal: display a view file
	 *
	 * @access	public
	 * @param   string $filename
	 */    
  public function _view($_tmvc_filepath)
  {
    if(!file_exists($_tmvc_filepath))
      trigger_error("Unknown file '$_tmvc_filepath'",E_USER_ERROR);

    // bring tpl vars into view scope
    extract($this->view_vars);
    include($_tmvc_filepath);
  }

}

?>
