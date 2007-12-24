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
 * TinyMVC_ErrorHandler
 * 
 * The MVC error handler
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TinyMVC_ErrorHandler
{
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct() { }
  
	/**
	 * trigger_error
	 *
	 * the error handler method used for all triggerable errors
	 *
	 * @access	public
	 */    
  function trigger_error($errno, $errstr, $errfile, $errline)
  {
    /* get instance of tmvc object */
    $tmvc = tmvc::instance();
    
    /* set error messages */
    $errors['errno'] = $errno;
    $errors['errstr'] = $errstr;
    $errors['errfile'] = $errfile;
    $errors['errline'] = $errline;
    $tmvc->view->view_vars['errors'] = $errors;
    /* display the error view */
    $tmvc->view->sysview('error_view');
    /* exit if not a notice */
    if($errno == E_USER_ERROR)
      exit(1);
  }
}

?>