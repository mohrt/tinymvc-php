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

}

?>
