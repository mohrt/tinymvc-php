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
 * TinyMVC_ErrorHandler
 * 
 * A simple exception handler to display exceptions in a formatted box.
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */
function TinyMVC_ErrorHandler($errno, $errstr, $errfile, $errline) {
  // do nothing if error reporting is turned off
  if (error_reporting() === 0)
  {
    return;
  }
  // be sure received error is supposed to be reported
  if (error_reporting() & $errno)
  {   
		throw new TinyMVC_ExceptionHandler($errstr, $errno, $errno, $errfile, $errline);
  }
}

?>