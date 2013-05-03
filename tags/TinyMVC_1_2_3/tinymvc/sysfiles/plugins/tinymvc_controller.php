<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007-2008 Monte Ohrt, All rights reserved.
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
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct()
  {
    /* save controller instance */
    tmvc::instance($this,'controller');
  
    /* instantiate load library */
    $this->load = new TinyMVC_Load;  

    /* instantiate view library */
    $this->view = new TinyMVC_View;
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
	 * __call
	 *
	 * gets called when an unspecified method is used
	 *
	 * @access	public
	 */    
  function __call($function, $args) {
  
    throw new Exception("Unknown controller method '{$function}'");

  }
  
}

?>
