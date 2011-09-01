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
  function __construct($poolname=null) {
    $this->db = tmvc::instance()->controller->load->database($poolname);
  }
  
}

?>
